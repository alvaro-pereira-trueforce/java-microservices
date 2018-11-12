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
    /**
     * UninstallIntegrationEvent constructor.
     * @param $data
     * @param ZendeskChannelService $linkedInChannelService
     */
    public function __construct($data, ZendeskChannelService $linkedInChannelService)
    {
        $this->service = $linkedInChannelService;
        parent::__construct($data);
    }

    /**
     * Delete All Integration Account for the Domain
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