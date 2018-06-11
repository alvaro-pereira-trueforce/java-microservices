<?php

namespace APIServices\Zendesk_Telegram\Services;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Services\ZendeskAPI;
use APIServices\Zendesk\Services\ZendeskClient;
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
     * Send Channel Service Update Messages To Zendesk Support Push Endpoint
     * @param  Update $update
     * @param  $telegram_token
     * @throws \Exception
     */
    public function sendUpdate($update, $telegram_token)
    {
        try {
            $updateType = $this->getMessageTypeInstance($update);
            $message = $updateType->getTransformedMessage();
            if ($message) {
                $pushService = $this->getZendeskAPIServiceInstance($telegram_token);
                $pushService->pushNewMessage($message);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $update
     * @return IMessageType
     * @throws \Exception
     */
    protected function getMessageTypeInstance($update)
    {
        try {
            $message_type = $this->telegram_service->detectMessageType($update);
            return App::makeWith($this->chanel_type . '.' . $message_type, [
                'update' => $update,
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $token
     * @return ZendeskAPI
     * @throws \Exception
     */
    protected function getZendeskAPIServiceInstance($token)
    {
        try {
            $account = $this->telegram_service->getAccountByToken($token);
            $api_client = App::makeWith(ZendeskClient::class, [
                'access_token' => $account['zendesk_access_token']
            ]);

            return App::makeWith(ZendeskAPI::class, [
                'subDomain' => $account['zendesk_app_id'],
                'client' => $api_client,
                'instance_push_id' => $account['instance_push_id']
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get all Update Messages using Polling Method
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
                //this must be retro-compatible but it wasn't test it because was deprecated with web hooks
                $commandData = $this->getStartedCommand($update);
                if (!$this->isCommand($update) && $commandData) {
                    $this->triggerCommand($commandData['command'], $commandData['state'], $update);
                    continue;
                }
                if ($this->isCommand($update) || $commandData) {
                    continue;
                }
                $updateType = $this->getMessageTypeInstance($update);
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
     * @param string $command
     * @param string $state
     * @param Update $update
     * @return mixed
     * @throws \Exception
     */
    public function triggerCommand($command, $state, $update)
    {
        try {
            return $this->telegram_service->triggerCommand($command, $state, $update);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param Update $update
     * @return array
     * @throws \Exception
     */
    public function getStartedCommand($update)
    {
        try {
            return $this->telegram_service->getStartedCommand($update);
        } catch (\Exception $exception) {
            throw $exception;
        }
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
    public function isCommand($update)
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