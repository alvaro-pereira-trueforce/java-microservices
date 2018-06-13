<?php

namespace APIServices\Zendesk\Models;


use App\Database\Eloquent\ModelUUID;

abstract class ChannelSettings extends ModelUUID
{
    protected $table = 'channels_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hello_message',
        'custom_hello_message',
        'required_user_info'
    ];

    /**
     * The attributes that are hidden on the serialization
     *
     * @var array
     */
    protected $hidden = ['uuid', 'created_at', 'updated_at', 'channel_uuid'];
}