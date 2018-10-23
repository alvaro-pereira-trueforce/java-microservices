<?php

namespace APIServices\Zendesk\Models\EventsTypes;

use Illuminate\Support\Facades\App;

class EventFactory
{
    /**
     * Get the correct Event Handler or Default
     * @param $event_name
     * @param $event_data
     * @return IEventType
     */
    public static function getEventHandler($event_name, $event_data)
    {
        try {
            return App::makeWith($event_name, [
                'data' => $event_data
            ]);
        } catch (\Exception $exception) {
            return App::makeWith(UnknownEvent::class, [
                'data' => $event_data
            ]);
        }
    }
}