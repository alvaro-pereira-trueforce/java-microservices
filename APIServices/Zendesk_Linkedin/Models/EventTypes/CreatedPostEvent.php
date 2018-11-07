<?php

namespace APIServices\Zendesk_Linkedin\Models\EventTypes;

use APIServices\Zendesk\Models\EventsTypes\EventType;
use APIServices\Zendesk_Linkedin\Jobs\ProcessZendeskCreatePostEvent;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

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
        $this->zendeskChannelService=$zendeskChannelService;

    }
    /**
     * put in line in the job all the new tickets events to be process after 24 hrs
     */
    function handleEvent()
    {
        Log::notice("New Ticket created in Zendesk");
        try {
            Log::debug($this->data);
            $thead_id = $this->data['external_id'];
            $validator_ticket = explode(':', $thead_id);
                $this->zendeskChannelService = App::make(ZendeskChannelService::class);
                $modelDatabase = $this->zendeskChannelService->getModelFromLinkedInIntegration($validator_ticket['1']);
                $params = json_decode($modelDatabase, true);
                Log::debug($params);
                ProcessZendeskCreatePostEvent::dispatch(1, $thead_id, $params,'Pull Likes/Followers')->delay(env('LINKEDIN_FOLLOWER_LIKES_RESPONSE'));
                Log::notice("Job process to track likes and followers Success.");

        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
        }

    }
}