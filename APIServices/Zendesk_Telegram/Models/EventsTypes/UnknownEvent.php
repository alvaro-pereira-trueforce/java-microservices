<?php

namespace APIServices\Zendesk_Telegram\Models\EventsTypes;


use Illuminate\Support\Facades\Log;

class UnknownEvent extends EventType
{
    function handleEvent()
    {
        Log::info($this->data);
    }
}