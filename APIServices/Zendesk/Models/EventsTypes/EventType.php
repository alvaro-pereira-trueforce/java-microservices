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
            if (array_key_exists('type_id', $data['events'][0]) &&
                array_key_exists('integration_name', $data['events'][0]) &&
                array_key_exists('subdomain', $data['events'][0]) &&
                array_key_exists('data', $data['events'][0])
            ) {
                $this->data = [
                    'type_id' => $data['events'][0]['type_id'],
                    'integration_name' => $data['events'][0]['integration_name'],
                    'subdomain' => $data['events'][0]['subdomain'],
                    'data' => $data['events'][0]['data']
                ];
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $this->data = $data;
        }
    }
}