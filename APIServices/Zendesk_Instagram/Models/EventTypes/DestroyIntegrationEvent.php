<?php

namespace APIServices\Zendesk_Instagram\Models\EventTypes;


use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk\Models\EventsTypes\EventType;
use APIServices\Zendesk\Repositories\ChannelRepository;
use Illuminate\Support\Facades\Log;

class DestroyIntegrationEvent extends EventType
{
    public function __construct($data, FacebookService $facebookService, ChannelRepository $channelRepository)
    {
        $this->service = $facebookService;
        $this->repository = $channelRepository;
        parent::__construct($data);

        if (array_key_exists('metadata', $this->data['data'])) {
            $this->data['metadata'] = json_decode($this->data['data']['metadata'], true);
        }
    }

    function handleEvent()
    {
        Log::notice("Delete Integration Account...");
        try {
            $this->repository->delete($this->data['metadata']['account_id']);
            $this->service->deletePageSubscriptionWebhook($this->data['metadata']['page_id'], $this->data['metadata']['page_access_token']);
            Log::notice("Delete Integration Success.");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}