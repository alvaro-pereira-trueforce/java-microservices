<?php

namespace APIServices\Telegram\Services;

use APIServices\Telegram\Repositories\ChannelRepository;
use APIServices\Zendesk\Utility;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class ChannelService {
    protected $database;

    protected $dispatcher;

    protected $repository;

    protected $telegramAPI;

    protected $zendeskUtils;

    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        ChannelRepository $repository,
        Api $telegramAPI,
        Utility $zendeskUtils
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
        $this->telegramAPI = $telegramAPI;
        $this->zendeskUtils = $zendeskUtils;
    }

    public function getAll($options = []) {
        return $this->repository->get($options);
    }

    public function getById($uuid, array $options = []) {
        $user = $this->getRequestedModel($uuid);

        return $user;
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
        $this->repository->delete($uuid);
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
            return $response;
        } catch (TelegramSDKException $exception) {
            return $exception;
        }
    }

    /**
     * @param $token
     * @return Api
     */
    private function getTelegramInstance($token) {
        return $this->telegramAPI->setAccessToken($token);
    }

    public function getTelegramUpdates($uuid) {
        $telegramModel = $this->repository->getByUUID($uuid);

        try {
            $telegram = $this->getTelegramInstance($telegramModel->token);
            $updates = $telegram->commandsHandler(false);
            $transformedMessages = [];
            foreach ($updates as $update) {
                $message_id = $update->getMessage()->get('message_id');
                $user_id = $update->getMessage()->getFrom()->get('id');
                $chat_id = $update->getMessage()->getChat()->get('id');
                $message = $update->getMessage()->get('text');
                $message_date = $update->getMessage()->get('date');
                $user_username = $update->getMessage()->getFrom()->get('username');
                $user_firstname = $update->getMessage()->getFrom()->get('first_name');
                $user_lastname = $update->getMessage()->getFrom()->get('last_name');

                array_push($transformedMessages, [
                    'external_id' => $this->zendeskUtils->getExternalID([$message_id, $user_id, $chat_id]),
                    'message' => $message,
                    'parent_id' => $user_id,
                    'created_at' => gmdate('Y-m-d\TH:i:s\Z', $message_date),
                    'author' => [
                        'external_id' => $this->zendeskUtils->getExternalID([$user_id, $user_username]),
                        'name' => $user_firstname.' '.$user_lastname
                    ]
                ]);
                Log::info($update);
            }

            $response = []
            return $transformedMessages;

        } catch (\Exception $exception) {
            return null;
        }
    }


}
