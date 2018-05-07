<?php

namespace APIServices\Telegram\Services;

use APIServices\Telegram\Repositories\TelegramRepository;
use APIServices\Zendesk\Utility;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Document;
use Telegram\Bot\Objects\PhotoSize;
use Telegram\Bot\Objects\Update;

class TelegramService {
    protected $database;

    protected $dispatcher;

    protected $repository;

    protected $telegramAPI;

    protected $zendeskUtils;

    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        TelegramRepository $repository,
        Api $telegramAPI,
        Utility $zendeskUtils,
        $uuid
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
        $this->telegramAPI = $telegramAPI;
        $this->zendeskUtils = $zendeskUtils;

        $token = $this->getTokenFromUUID($uuid);
        $this->telegramAPI->setAccessToken($token);
    }

    public function getAll($options = []) {
        return $this->repository->get($options);
    }

    public function getById($uuid, array $options = []) {
        $model = $this->getRequestedModel($uuid);

        return $model;
    }

    public function create($data) {
        $user = $this->repository->create($data);
        return $user;
    }

    public function update($uuid, array $data) {
        $model = $this->getRequestedModel($uuid);

        $this->repository->update($model, $data);

        return $model;
    }

    public function delete($uuid) {
        $model = $this->getById($uuid);
        return $model->delete();
    }

    private function getRequestedModel($uuid) {
        $model = $this->repository->getByUUID($uuid);

        if (is_null($model)) {
            throw new ModelNotFoundException();
        }
        return $model;
    }

    public function setWebhook($key) {
        try {
            $telegram = new Api($key);
            $url = env('APP_URL') . '/api/' . $key . '/webhook';
            Log::info($url);
            $response = $telegram->setWebhook(['url' => $url]);
            return $response;
        } catch (TelegramSDKException $exception) {
            return $exception;
        }
    }

    public function removeWebhook($key) {
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
    private function getTokenFromUUID($uuid) {
        $telegramModel = $this->repository->getByUUID($uuid);

        if ($telegramModel == null) {
            return "";
        }
        return $telegramModel->token;
    }

    /**
     * Get updates return all the messages from telegram converting the data for zendesk channel
     * pulling service.
     *
     * @return Update[]
     */
    public function getTelegramUpdates() {
        try {
            $updates = $this->telegramAPI->commandsHandler(false);
            return $updates;

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }

    /**
     * detect the type of the message
     *
     * @param Update
     * @return string
     */
    public function detectMessageType($update) {
        return $this->telegramAPI->detectMessageType($update);
    }

    /**
     * @param PhotoSize $fileSize
     * @return string
     */
    public function getPhotoURL($fileSize) {
        try {
            $token = $this->telegramAPI->getAccessToken();
            $file = $this->getFileWithID($fileSize['file_id']);
            return 'https://api.telegram.org/file/bot' . $token . '/' . $file->getFilePath();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "";
        }
    }

    /**
     * @param $file_id
     * @return \Telegram\Bot\Objects\File
     */
    public function getFileWithID($file_id) {
        return $this->telegramAPI->getFile(['file_id' => $file_id]);
    }

    /**
     * @param Document $document
     * @return string
     */
    public function getDocumentURL($document) {
        try
        {
            $token = $this->telegramAPI->getAccessToken();
            $file = $this->getFileWithID($document->getFileId());
            return 'https://api.telegram.org/file/bot' . $token . '/' . $file->getFilePath();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "";
        }
    }

    /**
     * @param $chat_id
     * @param $user_id
     * @param $message
     * @return string
     */
    public function sendTelegramMessage($chat_id, $user_id, $message) {
        try {
            $response = $this->telegramAPI->sendMessage([
                'chat_id' => $chat_id,
                'text' => $message
            ]);

            $message_id = $response->getMessageId();
            $user_id = $response->getFrom()->get('id');
            $chat_id = $response->getChat()->get('id');
            return $this->zendeskUtils->getExternalID([$user_id, $chat_id, $message_id]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "";
        }
    }

    /**
     * @param $token
     * @return null|\Telegram\Bot\Objects\User
     */
    public function checkValidTelegramBot($token) {
        try {
            $this->telegramAPI->setAccessToken($token);
            return $this->telegramAPI->getMe();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return null;
        }
    }

    public function registerNewIntegration($name, $token, $subdomain) {
        try {
            $model = $this->repository->create([
                'token' => $token,
                'zendesk_app_id' => $subdomain,
                'integration_name' => $name
            ]);
            return [
                'token' => $model->uuid,
                'integration_name' => $model->integration_name,
                'zendesk_app_id' => $model->zendesk_app_id
            ];
        } catch (QueryException $exception) {
            return ["error" => ""];
        } catch (\Exception $exception) {
            Log::info($exception);
            return null;
        }
    }

    public function getMetadataFromSavedIntegration($uuid) {
        $current_channel = $this->getById($uuid);
        return [
            'token' => $current_channel->uuid,
            'integration_name' => $current_channel->integration_name,
            'zendesk_app_id' => $current_channel->zendesk_app_id
        ];
    }

    public function getByZendeskAppID($subdomain) {
        $model = $this->repository->getModel();
        $channels = $model->where('zendesk_app_id', '=', $subdomain)->get();
        return $channels;
    }
}
