<?php

namespace APIServices\Telegram\Models;


use App\Database\Eloquent\Model;

class TelegramChannel extends Model
{
    protected $table = "telegram_channels";
    protected $primaryKey = "uuid";
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'zendesk_app_id',
    ];
}
