<?php
namespace APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes;

/**
 * This is an interface to handle the DestroyIntegrationEvent and UninstallIntegrationEvent
 * finally UnknownEven for default
 * Interface IEventType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes
 */
interface IEventType
{
    /**
     * @param $data
     * @return mixed
     */
    function handleEvent($data);

}