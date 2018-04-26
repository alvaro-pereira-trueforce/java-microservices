<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Storage;

abstract class MessageType implements IMessageType {

    protected $telegramService;
    protected $uuid;
    protected $zendeskUtils;

    public function __construct(TelegramService $telegramService, $uuid, Utility $zendeskUtils) {
        $this->telegramService = $telegramService;
        $this->uuid = $uuid;
        $this->zendeskUtils = $zendeskUtils;
    }

    public function getLocalURLFromExternalURL($external_url)
    {
        $contents = file_get_contents($external_url);
        $name = substr($external_url, strrpos($external_url, '/') + 1);
        Storage::put($name, $contents);
        return Storage::url($name);
    }
}