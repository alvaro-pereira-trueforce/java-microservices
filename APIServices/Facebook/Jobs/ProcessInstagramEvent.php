<?php

namespace APIServices\Facebook\Jobs;

use APIServices\Facebook\Models\Facebook;
use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk_Instagram\Models\Factories\MessageTypeFactory;
use APIServices\Zendesk_Instagram\Models\InstagramChannel;
use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use App\Traits\ArrayTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ProcessInstagramEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use ArrayTrait;

    protected $instagramChannel;
    protected $payload;
    protected $field_type;
    protected $triesCount;

    /**
     * Create a new job instance.
     * @param InstagramChannel $instagramChannel
     * @param array $payload
     * @param string $field_type
     * @param int $triesCount
     * @return void
     */
    public function __construct(InstagramChannel $instagramChannel, $field_type, $payload, $triesCount)
    {
        $this->instagramChannel = $instagramChannel;
        $this->payload = $payload;
        $this->field_type = $field_type;
        $this->triesCount = $triesCount;
    }

    /**
     * Execute the job.
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        Log::notice('Starting Job With: ' . $this->field_type);
        try {
            Log::notice('Log Worker');
            Log::notice($this->instagramChannel->uuid);
            Log::debug($this->payload);
            App::when(ChannelRepository::class)->needs('$channelModel')->give(new InstagramChannel());
            /** @var ZendeskChannelService $channelService */
            $channelService = App::make(ZendeskChannelService::class);

            $settings = $this->instagramChannel->settings()->firstOrNew([])->toArray();
            $settings = $this->cleanArray($settings);

            //Configure Facebook API
            App::when(Facebook::class)
                ->needs('$access_token')
                ->give($this->instagramChannel->page_access_token);

            /** @var FacebookService $facebookService */
            $facebookService = App::make(FacebookService::class);
            Log::notice('Starting Facebook Communication...');
            try {
                if ($facebookService->isFacebookLimitEnable())
                    throw new \Exception('Facebook limit reached.');

                if (empty($this->payload['media']['id'])) {
                    $comment = $facebookService->getInstagramCommentByID($this->payload['id']);
                    if(empty($comment['media']))
                    {
                        Log::error('This Message will be omitted is not exist on facebook');
                        return;
                    }
                    $this->payload['media'] = $comment['media'];
                }

                $media = $facebookService->getInstagramMediaByID($this->payload['media']['id']);

                if (empty($media) || empty($media['comments'])) {
                    Log::debug('The media does not exist or it has not comments');
                    return;
                }
            } catch (\Exception $exception) {
                Log::error('Facebook says: ' . $exception->getMessage() . 'this is the try number: ' . $this->triesCount);
                Log::error('Facebook Request Code: ' . $exception->getCode());
                if ($this->triesCount > 2) {
                    Log::error('Tries limit reached.');
                    return;
                }
                static:: dispatch($this->instagramChannel, $this->field_type, $this->payload, $this->triesCount + 1)->delay($this->triesCount * 60);
                return;
            }
            Log::notice('Success..Processing Data..');

            $this->payload['media'] = $media;

            $message = MessageTypeFactory::getMessageType($this->field_type, $this->payload, $settings);

            if (!empty($message)) {
                $transformedMessages = $message->getTransformedMessage();
                Log::debug($transformedMessages);
                if (!empty($transformedMessages)) {
                    //Configure Zendesk API and Zendesk Client
                    $channelService->configureZendeskAPI(
                        $this->instagramChannel->zendesk_access_token,
                        $this->instagramChannel->subdomain,
                        $this->instagramChannel->instance_push_id
                    );
                    Log::debug($transformedMessages);
                    $channelService->sendUpdate($transformedMessages);
                }
            }
        } catch (\ReflectionException $exception) {
            Log::error('Reflexion Code: ' . $exception->getCode());
            Log::error($exception->getMessage() . ' OnLine: ' . $exception->getLine() . ' ' . $exception->getFile());
            Log::error('class does not exist, new instagram message type added.');
        } catch (\Exception $exception) {
            Log::error('General Code: ' . $exception->getCode());
            Log::error($exception->getMessage() . ' OnLine: ' . $exception->getLine() . ' ' . $exception->getFile());
            //throw $exception;
        }
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error('Failed Code: ' . $exception->getCode());
        Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
    }
}
