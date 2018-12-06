<?php

namespace APIServices\Zendesk\Repositories;

use APIServices\Zendesk\Models\BasicChannelModel;
use APIServices\Zendesk\Models\ChannelSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChannelRepository
{
    /** @var BasicChannelModel */
    protected $channelModel;

    public function __construct($channelModel)
    {
        $this->channelModel = $channelModel;
    }

    /**
     * @param array $data
     * @param string $primaryKeyName
     * @param array $settings
     * @return Model
     * @throws \Exception
     */
    public function updateOrCreateChannelWithSettings(array $data, $primaryKeyName, array $settings)
    {
        try {
            $channelModel = $this->channelModel;
            $data = $channelModel->getValidDataToFill($data);
            $data['settings'] = $settings;
            $channelModel = $channelModel
                ->where($primaryKeyName, '=', $data[$primaryKeyName])
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

    /***
     * @param array $data
     * @throws \Exception
     * @return BasicChannelModel
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            /** @var BasicChannelModel $model */
            $model = $this->channelModel;
            $model->fill($data);
            $model->save();
            /** @var ChannelSettings $settings */
            $settings = App::make(ChannelSettings::class);
            $settings->fill($data['settings']);
            $model->settings()->save($settings);
            DB::commit();
            return $model->fresh(['settings']);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /***
     * @param BasicChannelModel $model
     * @param array $data
     * @throws \Exception
     * @return Model
     */
    public function update($model, array $data)
    {
        DB::beginTransaction();
        try {
            $model->update($model->getValidDataToFill($data));
            if (array_key_exists('settings', $data)) {
                /** @var ChannelSettings $settings */
                $settings = $model->settings()->first();
                if (!$settings) {
                    /** @var ChannelSettings $settings */
                    $settings = App::make(ChannelSettings::class);
                }
                $settingData = $settings->getValidDataToFill($data['settings'], $settings);
                $settings->fill($settingData);
                $model->settings()->save($settings);
            }
            DB::commit();
            return $model->fresh(['settings']);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function delete($uuid)
    {
        DB::beginTransaction();
        try {
            /** @var Model $model */
            $model = $this->getQueryByColumnName('uuid', $uuid)->first();
            if ($model)
                $model->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
        }
    }

    public function deleteAllByDomain($zendeskDomainName)
    {
        DB::beginTransaction();
        try {
            /** @var Collection $models */
            $models = $this->getModelsByColumnName('subdomain', $zendeskDomainName);
            Log::debug($models);
            foreach ($models as $model) {
                if ($model)
                    $model->delete();
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
        }
    }

    /**
     * Get Builder to make a select where query.
     * @param $columnName
     * @param $value
     * @return Builder
     */
    protected function getQueryByColumnName($columnName, $value)
    {
        return $model = $this->channelModel->where($columnName, '=', $value);
    }

    public function checkIfExist($columnName, $value)
    {
        try {
            /** @var Model $model */
            $model = $this->getQueryByColumnName($columnName, $value)->first();
            if ($model)
                return true;
            else
                return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    /**
     * @param $columnName
     * @param $value
     * @return Model
     */
    public function getModelByColumnName($columnName, $value)
    {
        return $this->getQueryByColumnName($columnName, $value)->first();
    }

    /**
     * @param $columnName
     * @param $value
     * @return Collection
     */
    public function getModelsByColumnName($columnName, $value)
    {
        return $this->getQueryByColumnName($columnName, $value)->get();
    }
}