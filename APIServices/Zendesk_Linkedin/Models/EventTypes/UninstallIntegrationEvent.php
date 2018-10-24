<?php

namespace APIServices\Zendesk_Linkedin\Models\EventTypes;

use APIServices\Zendesk\Models\EventsTypes\EventType;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use Illuminate\Support\Facades\Log;

/**
 * Class UninstallIntegrationEvent
 * @package APIServices\Zendesk_Linkedin\Models\EventTypes
 */
class UninstallIntegrationEvent extends EventType
{
    public function __construct($data, ZendeskChannelService $channelService)
    {
        $this->service = $channelService;
        parent::__construct($data);
    }

    /**
     * Delete all the Integrations accounts that belows a certain Subdomain
     * @return mixed|void
     */
    function handleEvent()
    {
        Log::notice("Delete All Integration Account... for the Domain");
        try {
            $identifierSubdomain = $this->data['subdomain'];
            $this->service->deleteByZendeskSubdomain($identifierSubdomain);
            Log::notice("Uninstall Integrations Success.");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}