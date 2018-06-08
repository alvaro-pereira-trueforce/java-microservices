<?php

namespace APIServices\Zendesk\Services;

use Illuminate\Support\Facades\Log;

class PushService
{
    protected $zendesk_app_id;
    protected $instance_push_id;
    protected $access_token;
    protected $client;

    public function __construct($zendesk_app_id, $instance_push_id, $zendesk_access_token, ZendeskClient $client)
    {
        $this->zendesk_app_id = $zendesk_app_id;
        $this->instance_push_id = $instance_push_id;
        $this->access_token = $zendesk_access_token;
        $this->client = $client;
    }

    public function pushNewMessage($message)
    {
        try
        {
            $body = $this->getPushBody($message);
            $response = $this->client->pushRequest($body, $this->zendesk_app_id, $this->access_token);
            Log::debug($response);
            return $response;
        }catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
        }
    }

    public function getPushBody($message)
    {
        return [
            'instance_push_id' => $this->instance_push_id,
            'external_resources' => [
                $message
            ],
            'state' => ""
        ];
    }
}