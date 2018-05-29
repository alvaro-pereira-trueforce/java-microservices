<?php

namespace APIServices\Facebook\Repositories;


use APIServices\Facebook\Models\UserRegistrationStatus;
use App\Database\Eloquent\RepositoryUUID;

class FacebookRepository extends RepositoryUUID {

    /**
     * @return UserRegistrationStatus
     */
    protected function getModel() {
        return new UserRegistrationStatus();
    }

    /**
     * @param array $condition
     * @param array $data
     * @return UserRegistrationStatus
     */
    public function updateOrCreate(array $condition, array $data) {
        $model = $this->getModel();
        $model = $model->updateOrCreate($condition,$data);
        return $model;
    }
}