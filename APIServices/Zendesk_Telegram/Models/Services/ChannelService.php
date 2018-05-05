<?php

namespace APIServices\Zendesk_Telegram\Models\Services;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Models\MessageTypes\IMessageType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ChannelService {

    protected $telegram_service;
    protected $zendeskUtils;
    protected $chanel_type;

    public function __construct(TelegramService $telegramService, Utility $zendeskUtils) {
        $this->telegram_service = $telegramService;
        $this->zendeskUtils = $zendeskUtils;
        $this->chanel_type = 'telegram';
    }

    /**
     * @return array
     */
    public function getUpdates() {
        $updates = $this->telegram_service->getTelegramUpdates();

        $transformedMessages = [];
        foreach ($updates as $update) {
            $message_type = $this->telegram_service->detectMessageType($update);

            // must have a buffer in the future to catch only the first 200 messages and send
            // it the leftover later. Maybe never happen an overflow.
            if (count($transformedMessages) > 199) {
                break;
            }

            try {
                /** @var $updateType IMessageType*/
                $updateType = App::makeWith($this->chanel_type . '.' . $message_type, [
                    'update' => $update
                ]);
                $message = $updateType->getTransformedMessage();
                if ($message)
                    array_push($transformedMessages, $message);
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
        return $transformedMessages;
    }
}