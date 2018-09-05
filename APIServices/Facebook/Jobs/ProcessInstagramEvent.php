<?php

namespace APIServices\Facebook\Jobs;

use APIServices\Facebook\Models\Facebook;
use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk\Models\IMessageType;
use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk\Services\ZendeskAPI;
use APIServices\Zendesk\Services\ZendeskClient;
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
        Log::debug('Starting Job With: ' . $this->field_type);
        try {
            Log::debug('Log Worker');
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

                $media = $facebookService->getInstagramMediaByID($this->payload['media']['id']);

                if (empty($media) || empty($media['comments'])) {
                    Log::debug('The media does not exist or it has not comments');
                    return;
                }
            } catch (\Exception $exception) {
                Log::error('Facebook says: ' . $exception->getMessage() . 'this is the try number: ' . $this->triesCount);
                if ($this->triesCount > 10) {
                    Log::error('Tries limit reached.');
                    return;
                }
                static:: dispatch($this->instagramChannel, $this->field_type, $this->payload, $this->triesCount + 1)->delay($this->triesCount * 10 * 60);
                return;
            }
            Log::notice('Success..Processing Data..');

            $this->payload['media'] = $media;
            /** @var IMessageType $message */
            $message = App::makeWith('instagram_' . $this->field_type, [
                'payload' => $this->payload,
                'settings' => $settings
            ]);

            $transformedMessages = $message->getTransformedMessage();
            Log::debug($transformedMessages);

            if (!empty($transformedMessages)) {
                //Configure Zendesk API and Zendesk Client

                App::when(ZendeskClient::class)
                    ->needs('$access_token')
                    ->give($this->instagramChannel->zendesk_access_token);
                App::when(ZendeskAPI::class)
                    ->needs('$subDomain')
                    ->give($this->instagramChannel->subdomain);
                App::when(ZendeskAPI::class)
                    ->needs('$instance_push_id')
                    ->give($this->instagramChannel->instance_push_id);
                $channelService->sendUpdate($transformedMessages);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
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
        Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
    }
}
