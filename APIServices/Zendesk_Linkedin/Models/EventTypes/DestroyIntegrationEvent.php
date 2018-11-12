<?php

namespace APIServices\Zendesk_Linkedin\Models\EventTypes;

use APIServices\Zendesk\Models\EventsTypes\EventType;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use Illuminate\Support\Facades\Log;

/**
 * Class DestroyIntegrationEvent
 * @package APIServices\Zendesk_Linkedin\Models\EventTypes
 */
class DestroyIntegrationEvent extends EventType
{
    /**
     * DestroyIntegrationEvent constructor.
     * @param $data
     * @param ZendeskChannelService $linkedInChannelService
     */
    public function __construct($data, ZendeskChannelService $linkedInChannelService)
    {
        parent::__construct($data);
        $this->service = $linkedInChannelService;

        if (array_key_exists('metadata', $this->data['data'])) {
            $this->data['metadata'] = json_decode($this->data['data']['metadata'], true);
        }
    }

    /**
     * Delete Integration Account
     */
    function handleEvent()
    {
        Log::notice("Delete Integration Account...");
        try {
            Log::debug($this->data);
            $account_id = $this->data['metadata']['account_id'];
            $this->service->deleteByZendeskIdIntegration($account_id);
            Log::notice("Delete Integration Success.");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}