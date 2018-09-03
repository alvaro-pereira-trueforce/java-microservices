<?php

namespace APIServices\Zendesk_Instagram\Models;


use APIServices\Zendesk\Models\BasicChannelModel;

class InstagramChannel extends BasicChannelModel
{
    protected $table = "instagram_channels";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'integration_name', 'instagram_id', 'page_id', 'subdomain', 'instance_push_id', 'zendesk_access_token', 'access_token', 'page_access_token', 'uuid'
    ];

    /**
     * Get the urls record associated with the Manifest.
     */
    public function settings()
    {
        return $this->hasOne(InstagramChannelSettings::class, 'channel_uuid', 'uuid');
    }
}