<?php

namespace APIServices\Zendesk_Linkedin\Models;
use APIServices\Zendesk\Models\BasicChannelModel;

/**
 * Class LinkedInChannel
 * @package APIServices\Zendesk_Linkedin\Models
 */
class LinkedInChannel extends BasicChannelModel
{
    /**
     * @var string
     */
    protected $table = 'linkedin_channels';
    /**
     * @var array
     */
    protected $fillable = [
        'integration_name',
        'subdomain',
        'instance_push_id',
        'zendesk_access_token',
        'access_token',
        'company_id',
        'expires_in',
        'uuid'

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    function settings()
    {
        return $this->hasOne(LinkedInChannelSetting::class, 'channel_uuid', 'uuid');
    }
}