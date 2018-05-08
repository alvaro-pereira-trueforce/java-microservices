<?php

namespace APIServices\Zendesk_Telegram\Services;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Models\MessageTypes\IMessageType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Message;

class ChannelService {

    protected $telegram_service;
    protected $zendeskUtils;
    protected $chanel_type;
    protected $ticketService;

    public function __construct(TelegramService $telegramService, Utility $zendeskUtils,
                                TicketService $ticketService) {
        $this->telegram_service = $telegramService;
        $this->zendeskUtils = $zendeskUtils;
        $this->chanel_type = 'telegram';
        $this->ticketService = $ticketService;
    }

    /**
     * @return array
     */
    public function getUpdates() {
        $updates = $this->telegram_service->getTelegramUpdates();
        $transformedMessages = [];
        foreach ($updates as $update) {
            // must have a buffer in the future to catch only the first 200 messages and send
            // it the leftover later. Maybe never happen an overflow.
            // Telegram API already catch only the first 100 messages
            if (count($transformedMessages) > 199) {
                break;
            }

            try {
                $message_type = $this->telegram_service->detectMessageType($update);
                $message = $update->getMessage();
                $parent_id = $this->getParentID($message);

                /** @var $updateType IMessageType */
                $updateType = App::makeWith($this->chanel_type . '.' . $message_type, [
                    'update' => $update,
                    'parent_id' => $parent_id
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

    /**
     * @param Message $message
     * @return string
     */
    protected function getParentID($message) {
        $reply = $message->getReplyToMessage();

        if ($reply) {
            $parent_id = $this->zendeskUtils->getExternalID([
                $reply->getChat()->get('id'),
                $reply->getFrom()->get('id')
            ]);
        } else {
            $parent_id = $this->zendeskUtils->getExternalID([
                $message->getChat()->getId(),
                $message->getFrom()->getId()
            ]);
        }

        return $this->ticketService->getValidParentID($parent_id);
    }
}