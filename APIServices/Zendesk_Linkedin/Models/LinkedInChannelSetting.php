<?php

namespace APIServices\Zendesk_Linkedin\Models;


use APIServices\Zendesk\Models\ChannelSettings;

class LinkedInChannelSetting extends ChannelSettings
{
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['LinkedInChannel'];

    /**
     * Get the channel that owns the Urls.
     */
    public function LinkedInChannel() {
        return $this->belongsTo(LinkedInChannel::class, 'channel_uuid', 'uuid')->withDefault();
    }

}