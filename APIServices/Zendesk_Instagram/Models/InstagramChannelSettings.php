<?php

namespace APIServices\Zendesk_Instagram\Models;


use APIServices\Zendesk\Models\ChannelSettings;

class InstagramChannelSettings extends ChannelSettings
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
        return $this->belongsTo(InstagramChannel::class, 'channel_uuid', 'uuid')->withDefault();
    }
}