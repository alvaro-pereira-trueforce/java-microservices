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
     * @param array $data
     * @throws \Exception
     * @return Model
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            /** @var TelegramChannel $model */
            $model = $this->getModel();
            $dataAccount = $this->getValidDataToFill($data, $model);
            $model->fill($dataAccount);
            $model->save();
            /** @var TelegramChannelSettings $settings */
            $settings = App::make(TelegramChannelSettings::class);
            if (empty($data['settings'])) {
                $data['settings'] = [];
            }
            $dataSettings = $this->getValidDataToFill($data['settings'], $settings);
            $settings->fill($dataSettings);
            $model->settings()->save($settings);
            DB::commit();
            return $model->fresh(['settings']);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /***
     * @param TelegramChannel $model
     * @param array $data
     * @throws \Exception
     * @return Model
     */
    public function update($model, array $data)
    {
        DB::beginTransaction();
        try {
            $dataAccount = $this->getValidDataToFill($data, $model);
            $model->update($dataAccount);
            if (array_key_exists('settings', $data)) {
                /** @var TelegramChannelSettings $settings */
                $settings = $model->settings()->first();
                if (!$settings) {
                    /** @var TelegramChannelSettings $settings */
                    $settings = App::make(TelegramChannelSettings::class);
                }
                if (empty($data['settings'])) {
                    $data['settings'] = [];
                }
                $dataSettings = $this->getValidDataToFill($data['settings'], $settings);
                $settings->fill($dataSettings);
                $model->settings()->save($settings);
            }
            DB::commit();
            return $model->fresh(['settings']);
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
    public function updateOrCreateChannelWithSettings(array $data)
    {
        try {
            $channelModel = $this->getModel();
            $channelModel = $channelModel
                ->where('uuid', '=', $data['uuid'])
                ->with('settings')
                ->first();
            if ($channelModel) {
                return $this->update($channelModel, $data);
            }
            $newRecord = $this->create($data);
            return $newRecord;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function updateAccountRegistration(array $data)
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
            /** @var TelegramChannel $model */
            $model = $model
                ->where('token', '=', $data['token'])->with('settings')->first();
            return $this->update($model, $data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get all the registered Accounts for a SubDomain
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
