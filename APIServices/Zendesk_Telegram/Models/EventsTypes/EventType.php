<?php

namespace APIServices\Zendesk_Telegram\Models\EventsTypes;


abstract class EventType implements IEventType
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
}