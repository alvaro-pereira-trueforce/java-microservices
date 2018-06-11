<?php

namespace APIServices\Telegram\Repositories;

use APIServices\Telegram\Models\TelegramChannel;
use App\Database\Eloquent\Model;
use App\Database\Eloquent\RepositoryUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class TelegramRepository extends RepositoryUUID
{
    /**
     * @return TelegramChannel
     */
    public function getModel()
    {
        return App::make(TelegramChannel::class);
    }

    /***
     * @param array $data ['token', 'zendesk_app_id']
     * @return TelegramChannel
     */
    public function create(array $data)
    {
        $model = $this->getModel();

        $model->fill($data);
        $model->save();

        return $model;
    }

    /***
     * @param Model $model
     * @param array $data ['token', 'zendesk_app_id']
     * @return Model
     */
    public function update(Model $model, array $data)
    {
        $model->fill($data);
        $model->save();
        return $model;
    }

    /**
     * @param $token
     * @return Builder
     */
    public function getByToken($token)
    {
        $model = $this->getModel();
        return $model->where('token', '=', $token);
    }

    /**
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function setAccountRegistration(array $data)
    {
        try {
            return $this->updateOrCreate($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function updateOrCreate(array $data)
    {
        try {
            $model = $this->getModel();
            $model = $model
                ->where('zendesk_app_id', '=', $data['zendesk_app_id'])
                ->whereNull('token')
                ->first();
            if ($model) {
                return $this->update($model, $data);
            }
            return $this->create($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $subdomain
     * @return Collection
     * @throws \Exception
     */
    public function getRegisteredByZendeskAppID($subdomain)
    {
        try {
            $model = $this->getModel();
            return $model
                ->where('zendesk_app_id', '=', $subdomain)
                ->whereNotNull('token')
                ->get();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $subdomain
     * @param $name
     * @return Collection
     * @throws \Exception
     */
    public function isNameRegistered($subdomain, $name)
    {
        try {
            $model = $this->getModel();
            return $model
                ->where('integration_name', '=', $name)
                ->where('zendesk_app_id', '=', $subdomain)
                ->first();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
