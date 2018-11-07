<?php

namespace APIServices\Zendesk_Linkedin\Factories;

use APIServices\Zendesk\Models\EventsTypes\EventFactory;
use APIServices\Zendesk\Models\EventsTypes\EventType;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use Illuminate\Support\Facades\Log;

/**
 * Class SubEventsFactory
 * @package APIServices\Zendesk_Linkedin\Factories
 */
class SubEventsFactory extends EventType
{
    /**
     * SubEventsFactory constructor.
     * @param $data
     * @param ZendeskChannelService $facebookService
     */
    public function __construct($data, ZendeskChannelService $facebookService)
    {
        $this->service = $facebookService;
        parent::__construct($data);

    }

    /**
     * Handle the subEvent
     */
    function handleEvent()
    {
        Log::debug('Zendesk SubEvent:');
        Log::debug($this->data);
        foreach ($this->data['data']['resource_events'] as $event) {
            /** @var IEventType $event */
            $subEventType = EventFactory::getEventHandler('linkedin_' . $event['type_id'], $event);
            $subEventType->handleEvent();
        }
    }
}