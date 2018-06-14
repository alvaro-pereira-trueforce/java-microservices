<?php

namespace APIServices\Zendesk_Telegram\Models\EventsTypes;

use APIServices\Telegram\Services\TelegramService;
use Illuminate\Support\Facades\Log;

class DestroyIntegrationEvent extends EventType
{

    protected $service;

    public function __construct($data, TelegramService $service)
    {
        parent::__construct($data);
        $this->service = $service;
    }

    function handleEvent()
    {
        try
        {
            $uuid = json_decode($this->data['info']['metadata'],true)['token'];
            $account = $this->service->getById($uuid);
            $account->delete();
            if($account)
            {
                $this->service->removeWebhook($account->token);
            }
        }catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
        }
    }
}