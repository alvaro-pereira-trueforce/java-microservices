<?php

namespace APIServices\Users\Events;

use App\Events\Event;
use APIServices\Users\Models\User;

class UserWasDeleted extends Event
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
