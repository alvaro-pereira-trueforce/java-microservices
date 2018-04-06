<?php

namespace APIServices\Users;

use App\Events\EventServiceProvider;
use APIServices\Users\Events\UserWasCreated;
use APIServices\Users\Events\UserWasDeleted;
use APIServices\Users\Events\UserWasUpdated;

class UserServiceProvider extends EventServiceProvider
{
    protected $listen = [
        UserWasCreated::class => [
            // listeners for when a user is created
        ],
        UserWasDeleted::class => [
            // listeners for when a user is deleted
        ],
        UserWasUpdated::class => [
            // listeners for when a user is updated
        ]
    ];
}
