<?php

namespace APIServices\Telegram\Models;


use App\Database\Eloquent\Model;
use App\Database\Traits\UUIDTrait;

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
        'token', 'zendesk_app_id',
    ];
}
