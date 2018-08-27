<?php

namespace APIServices\Zendesk_Instagram\Models\EventTypes;


use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk\Models\EventsTypes\EventType;
use Illuminate\Support\Facades\Log;

class DestroyIntegrationEvent extends EventType
{
    public function __construct($data, FacebookService $facebookService)
    {
        $this->service = $facebookService;
        parent::__construct($data);
    }

    function handleEvent()
    {
        try {
            $this->service->deletePageSubscriptionWebhook($this->data['metadata']['page_id'], $this->data['metadata']['page_access_token']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}