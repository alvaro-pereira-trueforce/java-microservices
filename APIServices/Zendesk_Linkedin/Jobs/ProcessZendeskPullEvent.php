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
     * @throws \Exception
     */
    public function handle($metadata)
    {
        Log::debug('Starting Job:');
        try {
            Log::debug('Log Worker');

            $linkedInService = App::make(LinkedinService::class);
            $comments=$linkedInService->getUpdates($metadata);
            $zendeskService= App::make(ZendeskChannelService::class);
            if (!empty($zendeskService->getFactoryMessageType($comments))){
                Log::debug('Update Body processed');

            }


            //Log::debug($tes);
            //dd($tes);
           // Log::debug($comments);
            //dd($comments);
        } catch (\Exception $exception) {
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