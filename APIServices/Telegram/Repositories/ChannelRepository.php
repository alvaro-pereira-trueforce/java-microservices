<?php

namespace APIServices\Telegram\Repositories;

use APIServices\Telegram\Models\TelegramChannel;
use App\Database\Eloquent\Model;
use App\Database\Eloquent\Repository;
use Illuminate\Support\Facades\App;

class ChannelRepository extends Repository
{
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

    public function getByUUID($uuid)
    {
        $model = $this->getModel();
        return $model->where('uuid', '=', $uuid)->first();
    }
}
