<?php

namespace APIServices\Zendesk\Models\EventsTypes;


use Illuminate\Support\Facades\Log;

abstract class EventType implements IEventType
{
    protected $data;
    protected $service;
    protected $repository;

    public function __construct($data)
    {
        try {
            if (array_key_exists('type_id', $data) &&
                array_key_exists('integration_name', $data) &&
                array_key_exists('subdomain', $data) &&
                array_key_exists('data', $data) && !empty($data['data']['metadata'])
            ) {
                $this->data = [
                    'type_id' => $data['type_id'],
                    'integration_name' => $data['integration_name'],
                    'subdomain' => $data['subdomain'],
                    'metadata' => json_decode($data['data']['metadata'], true)
                ];
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}