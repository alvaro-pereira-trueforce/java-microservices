<?php

namespace APIServices\Zendesk_Linkedin\Jobs;

use APIServices\LinkedIn\Services\LinkedinService;
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
     * ProcessZendeskPullEvent constructor.
     * @param LinkedInChannel $linkedInChannel
     * @param $triesCount
     */
    public function __construct(LinkedInChannel $linkedInChannel, $triesCount)
    {
        $this->linkedInChannel = $linkedInChannel;
        $this->triesCount = $triesCount;
    }

    /**
     * @param $metadata
     * @return mixed
     * @throws \Exception
     */
    public function handle($metadata)
    {
        Log::debug('Starting Job:');
        try {
            Log::debug('Log Worker');

            App::when(ChannelRepository::class)->needs('$channelModel')->give(new LinkedInChannel());
            /** @var ZendeskChannelService $channelService */
            $channelService = App::make(ZendeskChannelService::class);

            $linkedInService = App::make(LinkedinService::class);

            $comments = $linkedInService->getUpdates($metadata);
            if ((array_key_exists('_total', $comments) && (int)$comments['_total'] == 0)) {
                Log::debug('The media does not exist or it has not comments');
                return;
            } else {
                try {
                    $zendeskService = App::make(ZendeskChannelService::class);
                    $transformedMessages = $zendeskService->getMessageTransform($comments, $metadata['access_token']);
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
                        dd($transformedMessages);
                        $channelService->sendUpdate($transformedMessages);
                    }

                } catch (\Exception $exception) {
                    Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine().'error to transform messages');
                }
            }
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine().'error to instant channel services');
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