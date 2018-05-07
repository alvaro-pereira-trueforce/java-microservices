<?php

namespace App\Database\Eloquent;


abstract class RepositoryUUID extends Repository {

    /**
     * Get the first row that has the following uuid
     *
     * @param $uuid
     * @return Model
     */
    public function getByUUID($uuid) {
        $model = $this->getModel();
        return $model->where('uuid', '=', $uuid)->first();
    }

    /***
     * Basic Create Function
     * @param array $data
     * @return Model
     */
    public function create(array $data) {
        $model = $this->getModel();
        $model->fill($data);
        $model->save();
        return $model;
    }
}