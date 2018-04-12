<?php

namespace APIServices\Users\Repositories;

use APIServices\Users\Models\Users;
use App\Database\Eloquent\Repository;
use Illuminate\Support\Facades\App;

class UserRepository extends Repository
{
    public function getModel()
    {
        return App::make(Users::class);
    }

    public function create(array $data)
    {
        $user = $this->getModel();

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user->fill($data);
        $user->save();

        return $user;
    }

    public function update(Users $user, array $data)
    {
        $user->fill($data);

        $user->save();

        return $user;
    }
}
