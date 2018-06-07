<?php

namespace APIServices\Zendesk_Telegram\Services;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Models\MessageTypes\IMessageType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;

class ChannelService
{

    protected $telegram_service;
    protected $zendeskUtils;
    protected $chanel_type;

    public function __construct(TelegramService $telegramService, Utility $zendeskUtils)
    {
        $this->telegram_service = $telegramService;
        $this->zendeskUtils = $zendeskUtils;
        $this->chanel_type = 'telegram';
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
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
                $commandData = $this->telegram_service->getStartedCommand($update);
                if($commandData['state'] == 'send_zendesk')
                {

                }
                if (!$this->isCommand($update) && $commandData) {
                    $this->telegram_service->triggerCommand($commandData['command'], $commandData['state'], $update);
                    continue;
                }
                if($this->isCommand($update) || $commandData){
                    continue;
                }

                $message_type = $this->telegram_service->detectMessageType($update);
                /** @var $updateType IMessageType */
                $updateType = App::makeWith($this->chanel_type . '.' . $message_type, [
                    'update' => $update,
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
     * Send a channel back request
     *
     * @param string $parent_id Parent Identifier
     * @param string $message Message Text
     * @return string External Identifier
     * @throws \Exception
     */
    public function channelBackRequest($parent_id, $message)
    {
        try {
            $params = explode(':', $parent_id);
            $parent_uuid = $params[0];
            $chat_id = $params[1];
            $user_id = $params[2];
            $message_id = $params[3];

            $result = $this->telegram_service->sendTelegramMessage($chat_id, $message);

            return $this->zendeskUtils->getExternalID([
                $parent_uuid,
                $chat_id,
                $user_id,
                $result['message_id']
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param Update $update
     * @return bool
     */
    private function isCommand($update)
    {
        try {
            $message = $update->getMessage()->getText();
            preg_match('/^\/([^\s@]+)@?(\S+)?\s?(.*)$/', $message, $commands);
            if (!empty($commands)) {
                return true;
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }
}