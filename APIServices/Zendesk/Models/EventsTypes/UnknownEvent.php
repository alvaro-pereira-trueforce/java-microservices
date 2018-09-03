<?php

namespace APIServices\Zendesk\Models\EventsTypes;


use Illuminate\Support\Facades\Log;

class UnknownEvent extends EventType
{
    function handleEvent()
    {
        Log::notice($this->data);
    }
}