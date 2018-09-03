<?php

namespace APIServices\Zendesk\Models\EventsTypes;


interface IEventType
{
    function handleEvent();
}