<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use Illuminate\Support\Facades\Log;

/**
 * Class UninstallIntegrationEvent
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes
 */
class UninstallIntegrationEvent extends EventType
{
    /** @var ZendeskChannelService $channelService */
    protected $channelService;

    /**
     * UninstallIntegrationEvent constructor.
     * @param ZendeskChannelService $channelService
     */
    public function __construct(ZendeskChannelService $channelService)
    {
        $this->channelService = $channelService;
    }
    /**
     * Delete all the Integrations accounts that belows a certain Subdomain
     * @param $zendeskEvent
     * @return mixed|void
     */
    function handleEvent($zendeskEvent)
    {
        Log::notice("Delete All Integration Account... for the Domain");
        try {
            $identifierSubdomain = $this->getIdentifierSubdomain($zendeskEvent);
            $this->channelService->deleteByZendeskSubdomain($identifierSubdomain);
            Log::notice("Uninstall Integrations Success.");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}