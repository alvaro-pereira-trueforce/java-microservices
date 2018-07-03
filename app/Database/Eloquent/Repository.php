<?php

namespace App\Database\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Optimus\Genie\Repository as BaseRepository;

abstract class Repository extends BaseRepository {
    /**
     * Get a resource by its primary key
     *
     * @param  mixed $id
     * @param  array $options
     * @return Collection
     */
    public function getById($id, array $options = []) {
        $query = $this->createBaseBuilder($options);

        return $query->find($id);
    }

    /**
     * Delete a model looking for a instance of the class
     *
     * @param mixed $id
     * @return mixed
     */
    public function delete($id) {
        $query = $this->createQueryBuilder();
        $model = $query->where($this->getPrimaryKey($query), $id)->first();
        if ($model) {
            return $model->delete();
        }
        return 0;
    }

    /**
     * Clean the data array to fill just the fillable data
     * @param array $data
     * @param Model $model
     * @return array
     */
    public function getValidDataToFill($data, $model)
    {
        return array_intersect_key($data, array_flip($model->getFillable()));
    }
}
