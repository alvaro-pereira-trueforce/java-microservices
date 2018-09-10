<?php

namespace APIServices\Zendesk\Services;


use Illuminate\Support\Facades\Log;

class ZendeskAPI implements IZendeskAPI
{
    protected $pushService;
    protected $userService;

    public function __construct(ZendeskClient $client, $instance_push_id, $subDomain)
    {
        $this->pushService = new PushService($client, $instance_push_id, $subDomain);
    }

    function pushNewMessage($message)
    {
        $this->pushService->pushNewMessage($message);
    }

    function pushNewMessages($messages)
    {
        $this->pushService->pushNewMessages($messages);
    }
}