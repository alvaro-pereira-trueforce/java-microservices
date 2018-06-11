<?php

namespace APIServices\Zendesk\Services;

use Illuminate\Support\Facades\Log;

class PushService extends API
{

    public function pushNewMessage($message)
    {
        try
        {
            $body = $this->getPushBody($message);
            $response = $this->pushRequest($body);
            Log::debug($response);
            return $response;
        }catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
        }
    }

    /**
     *
     * @param $body
     * @return array
     * @throws \Exception
     */
    public function pushRequest($body)
    {
        try
        {
            $endpoint = $this->getPushEndpointWithDomain($this->subDomain);
            return $this->client->sendRequest($endpoint, $body);
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     * Get Endpoint
     * @param $subDomain
     * @return string
     */
    private function getPushEndpointWithDomain($subDomain)
    {
        return $this->client->getBasicEndpointWithSubDomain($subDomain).'any_channel/push';
    }

    protected function getPushBody($message)
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