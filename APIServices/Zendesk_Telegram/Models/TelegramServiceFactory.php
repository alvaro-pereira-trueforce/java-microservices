<?php

namespace APIServices\Zendesk_Telegram\Models;

use Illuminate\Support\Facades\App;
use Telegram\Bot\Api;
use APIServices\Telegram\Services\TelegramService;

class TelegramServiceFactory
{
    /**
     * @param Api $telegramAPI
     */
    public static function configureTelegramService($telegramAPI)
    {
        App::when(Api::class)
            ->needs('$token')
            ->give($telegramAPI->getAccessToken());
        App::when(TelegramService::class)
            ->needs('$telegramAPI')->give($telegramAPI);
    }
}