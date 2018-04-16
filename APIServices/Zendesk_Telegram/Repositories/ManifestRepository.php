<?php

namespace APIServices\Zendesk_Telegram\Repositories;

use App\Database\Eloquent\Repository;
use App\Database\Eloquent\Model;
use App\Database\Models\Manifest;
use Illuminate\Support\Facades\App;

class ManifestRepository extends Repository
{
    public function getModel()
    {
        return App::make(Manifest::class);
    }

    /***
     * @param array $data [name, id, author, version, admin_ui, pull_url, channelback_url,
     *                    clickthrough_url,
     *                    healthcheck_url]
     * @return Manifest
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
     * @param array $data [name, id, author, version, admin_ui, pull_url, channelback_url,
     *                    clickthrough_url,
     *                    healthcheck_url]
     * @return Model
     */
    public function update(Model $model, array $data)
    {
        $model->fill($data);
        $model->save();
        return $model;
    }
}
