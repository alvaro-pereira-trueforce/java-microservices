<?php

namespace APIServices\Zendesk_Telegram\Repositories;

use APIServices\Zendesk_Telegram\Models\TelegramChannel;
use APIServices\Zendesk_Telegram\Models\TelegramChannelSettings;
use App\Database\Eloquent\Model;
use App\Database\Eloquent\RepositoryUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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
     * @throws \Exception
     * @return TelegramChannel
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try
        {
            $model = $this->getModel();
            $model->fill($data);
            $model->save();
            $settings = App::make(TelegramChannelSettings::class);
            $model->settings()->save($settings);
            DB::commit();
            return $model;
        }catch (\Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }
    }

    /***
     * @param TelegramChannel $model
     * @param array $data ['token', 'zendesk_app_id']
     * @throws \Exception
     * @return Model
     */
    public function update($model, array $data)
    {
        DB::beginTransaction();
        try {
            $data = $this->getValidDataToFill($data, $model);
            $model->update($data);
            if(array_key_exists('settings', $data)){
                /** @var TelegramChannelSettings $settings */
                $settings = App::make(TelegramChannelSettings::class);
                $settingData = $this->getValidDataToFill($data['settings'], $settings);
                $settings->fill($settingData);
                $model->settings()->save($settings);
            }
            DB::commit();
            return $model;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
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
                ->whereNull('token')->with('settings')
                ->first();
            if ($model) {
                return $this->update($model, $data);
            }
            $newRecord = $this->create($data);
            return $newRecord;
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
