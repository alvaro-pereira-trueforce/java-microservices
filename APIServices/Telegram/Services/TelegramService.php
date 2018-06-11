<?php

namespace APIServices\Telegram\Services;

use APIServices\Telegram\Repositories\CommandHandlerRepository;
use APIServices\Telegram\Repositories\TelegramRepository;
use APIServices\Zendesk\Utility;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

class TelegramService
{
    protected $database;

    protected $dispatcher;

    protected $repository;

    protected $telegramAPI;

    protected $zendeskUtils;

    protected $commandRepository;

    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        TelegramRepository $repository,
        Api $telegramAPI,
        Utility $zendeskUtils,
        CommandHandlerRepository $commandRepository,
        $uuid
    )
    {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
        $this->telegramAPI = $telegramAPI;
        $this->zendeskUtils = $zendeskUtils;
        $this->commandRepository = $commandRepository;

        if ($uuid && $uuid != '') {
            $token = $this->getTokenFromUUID($uuid);
            $this->telegramAPI->setAccessToken($token);
        }
    }

    public function getAll($options = [])
    {
        return $this->repository->get($options);
    }

    public function getById($uuid, array $options = [])
    {
        $model = $this->getRequestedModel($uuid);

        return $model;
    }

    public function create($data)
    {
        $user = $this->repository->create($data);
        return $user;
    }

    public function update($uuid, array $data)
    {
        $model = $this->getRequestedModel($uuid);

        $this->repository->update($model, $data);

        return $model;
    }

    public function delete($uuid)
    {
        $model = $this->getById($uuid);
        return $model->delete();
    }

    private function getRequestedModel($uuid)
    {
        $model = $this->repository->getByUUID($uuid);

        if (is_null($model)) {
            throw new ModelNotFoundException();
        }
        return $model;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function setAccountRegistration(array $data)
    {
        try {
            $model = $this->repository->setAccountRegistration($data);
            if ($model)
                return $model->toArray();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $token
     * @return boolean
     * @throws \Exception
     */
    public function isTokenRegistered($token)
    {
        try {
            $model = $this->repository->getByToken($token)->first();
            if ($model)
                return true;
            return false;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $subdomain
     * @param $name
     * @return boolean
     * @throws \Exception
     */
    public function isNameRegistered($subdomain, $name)
    {
        try {
            $model = $this->repository->isNameRegistered($subdomain, $name);
            if ($model)
                return true;
            return false;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $token
     * @return \Telegram\Bot\TelegramResponse
     * @throws TelegramSDKException
     */
    public function setWebhook($token)
    {
        try {
            $telegram = new Api($token);
            $url = env('APP_URL') . '/api/' . $token . '/webhook';
            Log::debug($url);
            $response = $telegram->setWebhook(['url' => $url]);
            return $response;
        } catch (TelegramSDKException $exception) {
            throw $exception;
        }
    }

    public function removeWebhook($key)
    {
        try {
            $telegram = new Api($key);
            $response = $telegram->removeWebhook();
            return $response->getBody();
        } catch (TelegramSDKException $exception) {
            Log::error($exception->getMessage());
            return $exception;
        }
    }

    /**
     * @param $uuid
     * @return string token
     */
    private function getTokenFromUUID($uuid)
    {
        $telegramModel = $this->repository->getByUUID($uuid);

        if ($telegramModel == null) {
            return "";
        }
        return $telegramModel->token;
    }

    /**
     * @param $token
     * @return array
     * @throws \Exception
     */
    public function getAccountByToken($token)
    {
        try{
            return $this->repository->getByToken($token)->first()->toArray();
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     * Get updates return all the messages from telegram converting the data for zendesk channel
     * pulling service.
     *
     * @return Update[]
     */
    public function getTelegramUpdates()
    {
        try {
            $updates = $this->telegramAPI->commandsHandler(false);
            return $updates;

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }

    /**
     * Detect Message Type Based on Update or Message Object.
     *
     * @param Update|Message $object
     * @throws \Exception
     * @return string
     */
    public function detectMessageType($object)
    {
        try {
            if ($object instanceof Update) {
                if ($object->has('message')) {
                    $object = $object->getMessage();
                    $types = [
                        'audio', 'document', 'photo', 'sticker', 'video',
                        'voice', 'contact', 'location', 'text', 'left_chat_member',
                        'left_chat_participant', 'new_chat_participant', 'new_chat_member',
                        'new_user_info'
                    ];

                    $result = $object->keys()
                        ->intersect($types)
                        ->pop();
                    return $result;
                }

                if ($object->has('edited_message')) {
                    return 'edited';
                }
            }
            throw new \Exception('Unknown Type');
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Ask if the update or message has the specified type
     *
     * @param Update $update
     * @param string $type
     * @return bool
     */
    public function isMessageType($update, $type)
    {
        return $this->telegramAPI->isMessageType($type, $update);
    }

    /**
     * @param        $command_name
     * @param        $arguments
     * @param Update $update
     * @return mixed
     */
    public function triggerCommand($command_name, $arguments, $update)
    {
        return $this->telegramAPI->getCommandBus()->execute($command_name, $arguments, $update);
    }

    /**
     * @param $file_id
     * @return \Telegram\Bot\Objects\File
     */
    public function getFileWithID($file_id)
    {
        return $this->telegramAPI->getFile(['file_id' => $file_id]);
    }

    /**
     * @param $document_id
     * @return string
     * @throws $exception
     */
    public function getDocumentURL($document_id)
    {
        try {
            $token = $this->telegramAPI->getAccessToken();
            $file = $this->getFileWithID($document_id);
            return 'https://api.telegram.org/file/bot' . $token . '/' . $file->getFilePath();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param $chat_id
     * @param $message
     * @return array
     * @throws \Exception
     */
    public function sendTelegramMessage($chat_id, $message)
    {
        try {
            $response = $this->telegramAPI->sendMessage([
                'chat_id' => $chat_id,
                'text' => $message
            ]);

            $message_id = $response->getMessageId();
            $user_id = $response->getFrom()->get('id');
            $chat_id = $response->getChat()->get('id');

            return [
                'user_id' => $user_id,
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'message' => $message
            ];

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param $token
     * @return null|\Telegram\Bot\Objects\User
     */
    public function checkValidTelegramBot($token)
    {
        try {
            $this->telegramAPI->setAccessToken($token);
            return $this->telegramAPI->getMe();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return null;
        }
    }

    /**
     * @param $name
     * @param $token
     * @param $subdomain
     * @return array
     * @throws \Exception
     */
    public function registerNewIntegration($name, $token, $subdomain)
    {
        try {
            $model = $this->repository->setAccountRegistration([
                'token' => $token,
                'zendesk_app_id' => $subdomain,
                'integration_name' => $name
            ]);
            return [
                'token' => $model->uuid,
                'integration_name' => $model->integration_name,
                'zendesk_app_id' => $model->zendesk_app_id
            ];
        } catch (\Exception $exception) {
            Log::error($exception);
            throw $exception;
        }
    }

    public function getMetadataFromSavedIntegration($uuid)
    {
        $current_channel = $this->getById($uuid);
        return [
            'token' => $current_channel->uuid,
            'integration_name' => $current_channel->integration_name,
            'zendesk_app_id' => $current_channel->zendesk_app_id
        ];
    }

    /**
     * @param $subdomain
     * @return Collection
     * @throws \Exception
     */
    public function getByZendeskAppID($subdomain)
    {
        try {
            return $this->repository->getRegisteredByZendeskAppID($subdomain);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCurrentUUID()
    {
        try {
            $model = $this->repository->getByToken($this->telegramAPI->getAccessToken())->first();
            return $model->uuid;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Ask if the user started a command before and we are pending his response.
     * @var Update $update
     * @return array
     * @throws \Exception
     */
    public function getStartedCommand($update)
    {
        try {
            $user_id = $update->getMessage()->getFrom()->getId();
            $chat_id = $update->getMessage()->getChat()->getId();
            $commandModel = $this->commandRepository->getCommandWithUserAndChat($user_id, $chat_id);

            if ($commandModel)
                return [
                    'command' => $commandModel->command,
                    'state' => $commandModel->state,
                    'content' => $commandModel->content
                ];

            return null;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param Update $update
     * @throws \Exception
     */
    public function cancelStartedCommand($update)
    {
        try
        {
            $user_id = $update->getMessage()->getFrom()->getId();
            $chat_id = $update->getMessage()->getChat()->getId();
            $this->commandRepository->deleteWhereArray([
                'user_id' => $user_id,
                'chat_id' => $chat_id
            ]);
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     * @param Update $update
     * @param $command
     * @param $state
     * @param $content
     * @return array
     * @throws \Exception
     */
    public function setCommandProcess($update, $command, $state, $content = '')
    {
        try {
            $user_id = $update->getMessage()->getFrom()->getId();
            $chat_id = $update->getMessage()->getChat()->getId();

            $commandModel = $this->commandRepository->getCommandProcess($user_id, $chat_id, $command, $state, $content);
            if ($commandModel)
                return [
                    'command' => $commandModel->command,
                    'state' => $commandModel->state,
                    'content' => $commandModel->content
                ];

            return null;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
