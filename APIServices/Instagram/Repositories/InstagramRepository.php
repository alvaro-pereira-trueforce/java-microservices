<?php

namespace APIServices\Instagram\Repositories;

use APIServices\Instagram\Models\InstagramChannel;
use App\Database\Eloquent\Model;
use App\Database\Eloquent\Repository;
use Illuminate\Support\Facades\App;

class InstagramRepository extends Repository
{
    /**
     * @return InstagramChannel
     */
    public function getModel()
    {
        return App::make(InstagramChannel::class);
    }
    /***
     * @param array $data ['token', 'zendesk_app_id', 'instagram_id','page_id','integration_name']
     * @return InstagramChannel
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
