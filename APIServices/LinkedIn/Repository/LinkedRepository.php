<?php

namespace APIServices\LinkedIn\Repository;

use APIServices\Zendesk_Linkedin\Models\LinkedInChannel;
use App\Database\Eloquent\RepositoryUUID;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class LinkedRepository extends RepositoryUUID
{

    /**
     * @return LinkedInChannel
     */
    protected function getModel() {
        return App::make(LinkedInChannel::class);
    }

    /**
     * Get the last comment date by post_id
     *
     * @param $uuid
     * @return LinkedInChannel
     */
    public function findByID($uuid) {
        $model = $this->getModel();
        return $model->where('uuid', '=', $uuid)->first();
    }
    /**
     * @param array $data
     * @return LinkedInChannel
     */
    public function create(array $data)
    {
        $model = $this->getModel();
        $model->fill($data);
        $model->save();
        Log::debug('saved success');
        return $model;
    }

}