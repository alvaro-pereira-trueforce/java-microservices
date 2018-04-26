<?php
/**
 * Created by PhpStorm.
 * User: pablo.daza
 * Date: 4/26/18
 * Time: 9:54 AM
 */

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;

abstract class MessageType implements IMessageType {

    protected $telegramService;
    protected $uuid;
    protected $zendeskUtils;

    public function __construct(TelegramService $telegramService, $uuid, Utility $zendeskUtils) {
        $this->telegramService = $telegramService;
        $this->uuid = $uuid;
        $this->zendeskUtils = $zendeskUtils;
    }
}