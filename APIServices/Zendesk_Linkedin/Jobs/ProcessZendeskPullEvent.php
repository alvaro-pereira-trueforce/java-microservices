<?php

namespace APIServices\Zendesk_Linkedin\Jobs;

use APIServices\Zendesk_Linkedin\MessagesBuilder\Transformer;
use APIServices\Zendesk_Linkedin\Models\LinkedInChannel;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk\Services\ZendeskAPI;
use APIServices\Zendesk\Services\ZendeskClient;


/**
 * Class ProcessZendeskPullEvent
 * @package APIServices\Zendesk_Linkedin\Jobs
 */
class ProcessZendeskPullEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var LinkedInChannel
     */
    protected $linkedInChannel;

    /**
     * @var $lastUpdateDateSave
     */
    protected $lastUpdateDateSave;
    /**
     * @var $triesCount
     */
    protected $triesCount;
    /**
     * @var $metadata
     */
    protected $metadata;

    /**
     * @var $comments
     */
    protected $comments;
    /**
     * @var $nameJob
     */
    protected $nameJob;

    /**
     * ProcessZendeskPullEvent constructor.
     * @param LinkedInChannel $linkedInChannel
     * @param $triesCount
     * @param $nameJob
     * @param $metadata
     * @param $comments
     */
    public function __construct(LinkedInChannel $linkedInChannel, $triesCount, $nameJob, $metadata, $comments)
    {
        $this->linkedInChannel = $linkedInChannel;
        $this->triesCount = $triesCount;
        $this->metadata = $metadata;
        $this->comments = $comments;
        $this->nameJob = $nameJob;
    }

    /**
     * Send all the messages already transformed to the zendesks service
     */
    public function handle()
    {

        Log::debug('Starting Job: '.$this->nameJob);
        try {
            Log::debug('Log Worker');

            App::when(ChannelRepository::class)->needs('$channelModel')->give(new LinkedInChannel());
            /** @var ZendeskChannelService $channelService */
            $channelService = App::make(ZendeskChannelService::class);

            try {
                $zendeskTransformService = App::makeWith(Transformer::class, ['metadata' => $this->metadata]);
                $transformedMessages = $zendeskTransformService->getTransformedMessage($this->comments);
            } catch (\Exception $exception) {
                Log::error('LinkedIn says: ' . $exception->getMessage() . 'this is the try number: ' . $this->triesCount);
                if ($this->triesCount > 10) {
                    Log::error('Tries limit reached.');
                    return;
                }
                static:: dispatch($this->linkedInChannel, $this->triesCount + 1, $this->metadata)->delay($this->triesCount * 60);
            }
            Log::debug("communication with LinkedIn successful");
            if (!empty($transformedMessages)) {
                //Configure Zendesk API and Zendesk Client
                App::when(ZendeskClient::class)
                    ->needs('$access_token')
                    ->give($this->linkedInChannel->zendesk_access_token);
                App::when(ZendeskAPI::class)
                    ->needs('$subDomain')
                    ->give($this->linkedInChannel->subdomain);
                App::when(ZendeskAPI::class)
                    ->needs('$instance_push_id')
                    ->give($this->linkedInChannel->instance_push_id);
                $channelService->sendUpdate($transformedMessages);
            }

        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'error to instant channel services');
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