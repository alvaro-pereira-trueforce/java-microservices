<?php

namespace APIServices\Zendesk_Telegram\Models\EventsTypes;


interface IEventType
{
    function handleEvent();
}