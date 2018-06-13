<?php

namespace APIServices\Zendesk_Telegram\Models;


use APIServices\Zendesk\Models\ChannelSettings;

class TelegramChannelSettings extends ChannelSettings
{
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['TelegramChannel'];

    /**
     * Get the Telegram that owns the Urls.
     */
    public function TelegramChannel() {
        return $this->belongsTo(TelegramChannel::class, 'channel_uuid', 'uuid')->withDefault();
    }
}