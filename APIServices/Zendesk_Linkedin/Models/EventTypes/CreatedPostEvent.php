<?php

namespace APIServices\Zendesk_Linkedin\Models\EventTypes;

use APIServices\Zendesk\Models\EventsTypes\EventType;
use APIServices\Zendesk_Linkedin\Jobs\ProcessZendeskCreatePostEvent;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use Illuminate\Support\Facades\Log;

/**
 * Class CreatedPostEvent
 * @package APIServices\Zendesk_Linkedin\Models\EventTypes
 */
class CreatedPostEvent extends EventType
{
    /**
     * @var $data
     */
    protected $data;

    /**
     * @var ZendeskChannelService
     */
    protected $zendeskChannelService;

    /**
     * CreatedPostEvent constructor.
     * @param $data
     * @param ZendeskChannelService $zendeskChannelService
     */
    public function __construct($data, ZendeskChannelService $zendeskChannelService)
    {
        $this->data = $data;
        $this->zendeskChannelService = $zendeskChannelService;

    }

    /**
     * put in line in the job all the new tickets events to be process after 24 hrs
     */
    function handleEvent()
    {
        Log::debug("New Ticket created in Zendesk .....");
        try {
            $thead_id = $this->data['external_id'];
            $validator_ticket = explode(':', $thead_id);
            $modelDatabase = $this->zendeskChannelService->getModelFromLinkedInIntegration($validator_ticket['1']);
            $params = json_decode($modelDatabase, true);
            ProcessZendeskCreatePostEvent::dispatch(1, $thead_id, $params, 'Pull Likes/Followers')->delay(env('LINKEDIN_FOLLOWER_LIKES_TRACKING_TIME'));
            Log::debug("Job process to track likes and followers Success.");

        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
        }
    }
}