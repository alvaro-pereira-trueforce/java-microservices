<?php

namespace APIServices\Facebook\Models;

use App\Database\Eloquent\ModelUUID;

class UserRegistrationStatus extends ModelUUID
{
    protected $table = 'user_registration_status';
    protected $fillable = [
        'zendesk_domain_name',
        'integration_name',
        'facebook_token',
        'status'
    ];
}