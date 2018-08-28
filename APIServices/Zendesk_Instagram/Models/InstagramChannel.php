<?php

namespace APIServices\Zendesk_Instagram\Models;


use App\Database\Eloquent\ModelUUID;
use Ramsey\Uuid\Uuid;

class InstagramChannel extends ModelUUID
{
    protected $table = "instagram_channels";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'integration_name', 'instagram_id', 'page_id', 'subdomain', 'instance_push_id', 'zendesk_access_token', 'access_token', 'page_access_token', 'account_id'
    ];

    protected $hidden = ['uuid'];

    public function getAccountIdAttribute()
    {
        return $this->uuid;
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['uuid'] = $value;
    }

    /**
     * Get the urls record associated with the Manifest.
     */
    public function settings()
    {
        return $this->hasOne(InstagramChannelSettings::class, 'channel_uuid', 'uuid');
    }

    //This would be delete the setting record if the channel is deleted
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = (string)Uuid::uuid4()->toString();
        });

        static::deleting(
            function ($telegram_channel) {
                $telegram_channel->settings()->delete();
            }
        );
    }
}