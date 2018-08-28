<?php

namespace APIServices\Zendesk\Models\EventsTypes;


abstract class EventType implements IEventType
{
    protected $data;
    protected $service;
    protected $repository;

    public function __construct($data)
    {
        $this->data = [
            'type_id' => $data['type_id'],
            'integration_name' => $data['integration_name'],
            'subdomain' => $data['subdomain'],
            'metadata' => json_decode($data['data']['metadata'], true)
        ];
    }
}