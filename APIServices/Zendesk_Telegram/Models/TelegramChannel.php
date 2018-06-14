<?php

namespace APIServices\Zendesk_Telegram\Models;

use App\Database\Eloquent\Model;
use App\Database\Traits\UUIDTrait;
use Ramsey\Uuid\Uuid;

class TelegramChannel extends Model
{
    protected $table = "telegram_channels";

    use UUIDTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'zendesk_app_id', 'integration_name', 'zendesk_access_token', 'instance_push_id'
    ];

    /**
     * Get the urls record associated with the Manifest.
     */
    public function settings()
    {
        return $this->hasOne(TelegramChannelSettings::class, 'channel_uuid', 'uuid');
    }

    //This would be delete the setting record if the channel is deleted
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4()->toString();
        });

        static::deleting(
            function ($telegram_channel) {
                $telegram_channel->settings()->delete();
            }
        );
    }
}
