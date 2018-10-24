<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use Illuminate\Support\Facades\Log;

/**
 * Class DestroyIntegrationEvent
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes
 */
class DestroyIntegrationEvent extends EventType
{
    /** @var ZendeskChannelService $channelService */
    protected $channelService;

    /**
     * DestroyIntegrationEvent constructor.
     * @param ZendeskChannelService $channelService
     */
    public function __construct(ZendeskChannelService $channelService)
    {
        $this->channelService = $channelService;
    }
    /**
     * Delete one Integration whether a company or
     * a product account through their account_id
     * @param $zendeskEvent
     * @return mixed|void
     */
    function handleEvent($zendeskEvent)
    {
        Log::notice("Delete Integration Account...");
        try {
            $idenfifierId = $this->getIdentifierId($zendeskEvent);
            $this->channelService->deleteByZendeskIdIntegration($idenfifierId['account_id']);
            Log::notice("Delete Integration Success.");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}