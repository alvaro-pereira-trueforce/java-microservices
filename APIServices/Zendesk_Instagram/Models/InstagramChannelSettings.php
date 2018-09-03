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
    protected $touches = ['InstagramChannel'];

    /**
     * Get the channel that owns the Urls.
     */
    public function InstagramChannel() {
        return $this->belongsTo(InstagramChannel::class, 'channel_uuid', 'uuid')->withDefault();
    }
}