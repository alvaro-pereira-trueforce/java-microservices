<?php

namespace APIServices\Zendesk_Linkedin\Jobs;

use APIServices\LinkedIn\Services\LinkedinService;
use APIServices\Zendesk_Linkedin\MessagesBuilder\TransformTimestampSearcher;
use APIServices\Zendesk_Linkedin\Models\LinkedInChannel;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use APIServices\Zendesk\Repositories\ChannelRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use APIServices\Utilities\StringUtilities;
use Carbon\Carbon;


/**
 * Class ProcessZendeskCreatePostEvent
 * @package APIServices\Zendesk_Linkedin\Jobs
 */
class ProcessZendeskCreatePostEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var LinkedInChannel
     */
    protected $linkedInChannel;
    /**
     * @var $triesCount
     */
    protected $triesCount;
    /**
     * @var $metadata
     */
    protected $metadata;

    /**
     * @var $thead_id
     */
    protected $thead_id;

    /**
     * @var $zendeskChannelService
     */
    protected $zendeskChannelService;

    /**
     * @var array $company_id
     */
    protected $company_id;
    /**
     * @var $nameJob
     */
    protected $nameJob;

    /**
     * @var $followersAmount
     */
    protected $followersAmount;

    /**
     * @var $likesAmountValid
     */
    protected $likesAmountValid;

    /**
     * ProcessZendeskCreatePostEvent constructor.
     * @param $triesCount
     * @param $thead_id
     * @param $metadata
     * @param $nameJob
     */
    public function __construct($triesCount, $thead_id, $metadata, $nameJob)
    {
        $this->metadata = $metadata;
        $this->triesCount = $triesCount;
        $this->thead_id = $thead_id;
        $this->nameJob = $nameJob;
    }

    public function handle()
    {

        Log::debug('Starting Job:' . $this->nameJob);
        try {
            Log::debug('Log Worker');
            App::when(ChannelRepository::class)->needs('$channelModel')->give(new LinkedInChannel());
            /** @var ZendeskChannelService $channelService */
            $channelService = App::make(ZendeskChannelService::class);

            $linkedInService = App::make(LinkedinService::class);
            $this->zendeskChannelService = App::make(ZendeskChannelService::class);
            try {
                $likesAmount = $linkedInService->getLinkedInLikes($this->metadata, $this->thead_id);
                $this->followersAmount = $linkedInService->getLinkedInFollowers($this->metadata, $this->thead_id);
                if ($likesAmount == null) {
                    $this->likesAmountValid = 0;
                } else {
                    $this->likesAmountValid = $likesAmount['_total'];
                }
            } catch (\Exception $exception) {
                Log::error('LinkedIn says: ' . $exception->getMessage() . 'this is the try number: ' . $this->triesCount);
                if ($this->triesCount > 2) {
                    Log::error('Tries limit reached.');
                    return;
                }
                static:: dispatch($this->triesCount + 1, $this->thead_id, $this->metadata)->delay(env('LINKEDIN_FOLLOWER_LIKES_RESPONSE') * $this->triesCount);
            }
            $channelService->configureZendeskAPI($this->metadata['zendesk_access_token'], $this->metadata['subdomain'], $this->metadata['instance_push_id']);
            $channelService->sendUpdate([
                    [
                        'external_id' => StringUtilities::RandomString(),
                        'message' => 'The following  LinkedIn Account has ' . $this->likesAmountValid . ' likes.
                          The company page has ' . $this->followersAmount . ' followers',
                        'thread_id' => $this->thead_id,
                        'created_at' => date('Y-m-d\TH:i:s\Z'),
                        'author' => [
                            'external_id' => StringUtilities::RandomString(),
                            'name' => 'LinkedInAdministration'
                        ]
                    ]
                ]
            );
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'error to instant channel services');
        }
        try {
            $limitTracking = App::makeWith(TransformTimestampSearcher::class, ['params' => $this->metadata, 'treads' => $this->thead_id]);
            if ((Carbon::now()->diffInSeconds($limitTracking->searchTimestampByIdComment())) < env('LINKEDIN_TRACKING_EXPIRATION_TIME')) {
                static:: dispatch($this->triesCount, $this->thead_id, $this->metadata, $this->nameJob)->delay(env('LINKEDIN_FOLLOWER_LIKES_TRACKING_TIME'));
            }
        } catch (\Exception $exception) {
            Log::error('LinkedIn says: ' . $exception->getMessage() . 'this is the try number: ' . $this->triesCount);
            if ($this->triesCount > 2) {
                Log::error('Tries limit reached.');
                return;
            }
            static:: dispatch($this->triesCount + 1, $this->thead_id, $this->metadata)->delay(env('LINKEDIN_FOLLOWER_LIKES_TRACKING_TIME') * $this->triesCount);
        }
    }

}