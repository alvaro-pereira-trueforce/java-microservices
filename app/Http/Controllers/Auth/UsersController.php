<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Optimus\Bruno\EloquentBuilderTrait;
use Optimus\Bruno\LaravelController;

class UsersController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getUsers(Request $request)
    {
        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        // Start a new query for users using Eloquent query builder
        // (This would normally live somewhere else, e.g. in a Repository)
        $query = User::query();
        $this->applyResourceOptions($query, $resourceOptions);
        $users = $query->get();

        // Parse the data using Optimus\Architect
        $parsedData = $this->parseData($users, $resourceOptions, 'users');

        // Create JSON response of parsed data
        return $this->response($parsedData);
    }
}
