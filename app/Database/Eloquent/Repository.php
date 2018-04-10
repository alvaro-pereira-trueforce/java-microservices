<?php

namespace App\Database\Eloquent;

use Optimus\Genie\Repository as BaseRepository;

abstract class Repository extends BaseRepository
{
    /**
     * Get a resource by its primary key
     * @param  mixed $id
     * @param  array $options
     * @return Model
     */
    public function getById($id, array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        return $query->find($id);
    }
}
