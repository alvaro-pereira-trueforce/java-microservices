<?php

namespace APIServices\Zendesk\Services;


abstract class API
{
    protected $client;
    protected $instance_push_id;
    protected $subDomain;
    public function __construct(ZendeskClient $client, $instance_push_id, $subDomain)
    {
        $this->client = $client;
        $this->instance_push_id = $instance_push_id;
        $this->subDomain = $subDomain;
    }
}