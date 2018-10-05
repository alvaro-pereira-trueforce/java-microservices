<?php


namespace APIServices\Zendesk_Linkedin\Models;
use APIServices\Zendesk\Models\BasicChannelModel;


class LinkedInChannel extends BasicChannelModel
{
    protected $table = 'linkedin_channels';

    /**
     * The attributes that are mass assignable.
     *
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

    function settings()
    {
        return $this->hasOne(LinkedInChannelSetting::class, 'channel_uuid', 'uuid');
    }
}