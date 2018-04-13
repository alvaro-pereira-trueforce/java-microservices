<?php

namespace APIServices\Users\Events;

use App\Events\Event;
use APIServices\Users\Models\Users;

class UserWasDeleted extends Event
{
    public $user;

    public function __construct(Users $user)
    {
        $this->user = $user;
    }
}
