<?php

namespace APIServices\Telegram\Models;

use App\Database\Eloquent\ModelUUID;

class CommandHandler extends ModelUUID
{
    protected $table = 'telegram_command_handler';
    protected $fillable = [
        'user_id',
        'chat_id',
        'state',
        'command',
        'content'
    ];
}
