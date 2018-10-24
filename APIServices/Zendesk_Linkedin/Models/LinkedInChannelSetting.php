<?php

namespace APIServices\Zendesk_Linkedin\Models;
use APIServices\Zendesk\Models\ChannelSettings;

/**
 * Class LinkedInChannelSetting
 * @package APIServices\Zendesk_Linkedin\Models
 */
class LinkedInChannelSetting extends ChannelSettings
{
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['LinkedInChannel'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function LinkedInChannel() {
        return $this->belongsTo(LinkedInChannel::class, 'channel_uuid', 'uuid')->withDefault();
    }

}