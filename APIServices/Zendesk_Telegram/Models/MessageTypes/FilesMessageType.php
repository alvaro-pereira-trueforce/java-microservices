<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Storage;

abstract class FilesMessageType extends MessageType {

    protected $telegramService;

    public function __construct(Utility $zendeskUtils, $update, TelegramService $telegramService,
                                $state, $parent_id) {
        parent::__construct($zendeskUtils, $update, $state, $parent_id);
        $this->telegramService = $telegramService;
    }

    public function getLocalURLFromExternalURL($external_url) {
        $contents = file_get_contents($external_url);
        $name = substr($external_url, strrpos($external_url, '/') + 1);
        Storage::put($name, $contents);
        return Storage::url($name);
    }
}