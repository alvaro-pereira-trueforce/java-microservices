<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes;

/**
 * Class TEventType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes
 */
class TEventType
{
    /**
     * @var DestroyIntegrationEvent
     */
    protected $destroyIntegrationEvent;
    /**
     * @var UninstallIntegrationEvent
     */
    protected $uninstallIntegrationEvent;
    /**
     * @var UnknownEvent
     */
    protected $unknownEvent;

    /**
     * TEventType constructor.
     * @param DestroyIntegrationEvent $destroyIntegrationEvent
     * @param UninstallIntegrationEvent $uninstallIntegrationEvent
     * @param UnknownEvent $unknownEvent
     */
    public function __construct(DestroyIntegrationEvent $destroyIntegrationEvent, UninstallIntegrationEvent $uninstallIntegrationEvent, UnknownEvent $unknownEvent)
    {
        $this->destroyIntegrationEvent = $destroyIntegrationEvent;
        $this->uninstallIntegrationEvent = $uninstallIntegrationEvent;
        $this->unknownEvent = $unknownEvent;
    }

    /**
     * The EventBuilder build the respective whether DestroyIntegrationEvent or UninstallIntegrationEvent,
     * UnknownEvent by default
     * @param $type_id
     * @param $request
     */
    public function EventBuilder($type_id, $request)
    {
        if ($type_id == 'linkedin_destroy_integration_instance') {
            $this->destroyIntegrationEvent->handleEvent($request);
        } else if ($type_id == 'linkedin_destroy_integration') {
            $this->uninstallIntegrationEvent->handleEvent($request);
        } else {
            $this->unknownEvent->handleEvent($request);
        }
    }

}