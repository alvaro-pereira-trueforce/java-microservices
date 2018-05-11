<?php

namespace APIServices\Telegram\Repositories;

use APIServices\Telegram\Models\TelegramChannel;
use App\Database\Eloquent\Model;
use App\Database\Eloquent\RepositoryUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class TelegramRepository extends RepositoryUUID {
    /**
     * @return TelegramChannel
     */
    public function getModel() {
        return App::make(TelegramChannel::class);
    }

    /***
     * @param array $data ['token', 'zendesk_app_id']
     * @return TelegramChannel
     */
    public function create(array $data) {
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
    public function update(Model $model, array $data) {
        $model->fill($data);
        $model->save();
        return $model;
    }

    /**
     * @param $token
     * @return Builder
     */
    public function getByToken($token) {
        $model = $this->getModel();
        return $model->where('token', '=', $token);
    }
}
