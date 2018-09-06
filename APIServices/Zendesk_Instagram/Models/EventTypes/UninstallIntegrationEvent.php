<?php

namespace APIServices\Zendesk_Instagram\Models\EventTypes;


use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk\Models\EventsTypes\EventType;
use APIServices\Zendesk\Repositories\ChannelRepository;
use Illuminate\Support\Facades\Log;

class UninstallIntegrationEvent extends EventType
{

    public function __construct($data, FacebookService $facebookService, ChannelRepository $channelRepository)
    {
        $this->service = $facebookService;
        $this->repository = $channelRepository;
        parent::__construct($data);
    }

    function handleEvent()
    {
        Log::notice("Delete All Integration Account... for the Domain");
        try {
            $accounts = $this->repository->getModelsByColumnName('subdomain', $this->data['subdomain']);
            foreach ($accounts as $account) {
                try {
                    $this->service->deletePageSubscriptionWebhook($account->page_id, $account->page_access_token);
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
                try {
                    $account->delete();
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
            Log::notice("Delete Integrations Success.");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}