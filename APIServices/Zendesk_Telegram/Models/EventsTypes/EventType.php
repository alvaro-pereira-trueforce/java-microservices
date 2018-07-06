<?php

namespace APIServices\Zendesk_Telegram\Models\EventsTypes;

use APIServices\Telegram\Services\TelegramService;

abstract class EventType implements IEventType
{
    protected $data;
    protected $service;

    public function __construct($data, TelegramService $service)
    {
        $this->data = $data;
        $this->service = $service;
    }
}