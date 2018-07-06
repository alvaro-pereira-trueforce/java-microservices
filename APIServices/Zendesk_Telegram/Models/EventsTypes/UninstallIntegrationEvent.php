<?php

namespace APIServices\Zendesk_Telegram\Models\EventsTypes;


use Illuminate\Support\Facades\Log;

class UninstallIntegrationEvent extends EventType
{

    function handleEvent()
    {
        try
        {
            $subdomain = $this->data['subdomain'];
            $accounts = $this->service->getByZendeskAppID($subdomain);
            Log::debug($accounts);
            foreach ($accounts as $account)
            {
                $account->delete();
                if($account)
                {
                    $this->service->removeWebhook($account->token);
                }
            }
        }catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
        }
    }
}