<?php

namespace APIServices\Telegram\Services;

use APIServices\Telegram\Repositories\ChannelRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class ChannelService
{
    protected $database;

    protected $dispatcher;

    protected $repository;

    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        ChannelRepository $repository
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
    }

    public function getAll($options = [])
    {
        return $this->repository->get($options);
    }

    public function getById($uuid, array $options = [])
    {
        $user = $this->getRequestedModel($uuid);

        return $user;
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
        $this->repository->delete($uuid);
    }


    private function getRequestedModel($uuid)
    {
        $model = $this->repository->getById($uuid);

        if (is_null($model)) {
            throw new ModelNotFoundException();
        }
        return $model;
    }

    public function setWebhook($key)
    {
        try
        {
            $telegram = new Api($key);
            $url = env('APP_URL').'/api/'.$key.'/webhook';
            Log::info($url);
            $response = $telegram->setWebhook(['url' => $url]);
            return $response;
        }catch (TelegramSDKException $exception)
        {
            return $exception;
        }
    }
}
