<?php

namespace APIServices\Channel_Integration\Services;


use APIServices\Zendesk\Services\ZendeskAPI;
use APIServices\Zendesk\Utility;

abstract class CommonChannelService implements IChannelService
{
    protected $zendeskUtils;
    protected $zendesk_api;

    public function __construct(Utility $zendesk_utils, ZendeskAPI $zendesk_api)
    {
        $this->zendeskUtils = $zendesk_utils;
        $this->zendesk_api = $zendesk_api;
    }
}