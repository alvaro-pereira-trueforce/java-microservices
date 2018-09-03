<?php

namespace APIServices\Zendesk\Models;


use App\Database\Eloquent\ModelUUID;

abstract class BasicChannelModel extends ModelUUID
{
    abstract function settings();

    //This would be delete the setting record if the channel is deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(
            function ($channel) {
                $channel->settings()->delete();
            }
        );
    }
}