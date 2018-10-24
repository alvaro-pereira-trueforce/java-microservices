<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes;
use Illuminate\Support\Facades\Log;

class UnknownEvent extends EventType
{
    /**
     * This is the default event
     * @param $data
     * @return mixed
     */
    function handleEvent($data)
    {
        Log::notice($data);
    }
}