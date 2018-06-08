<?php

namespace APIServices\Zendesk\Services;


use GuzzleHttp\Client;

class ZendeskClient
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     *
     * @param $body
     * @param $subDomain
     * @param $access_token
     * @return array
     * @throws \Exception
     */
    public function pushRequest($body, $subDomain, $access_token)
    {
        try
        {
            $headers = [
                'Authorization' => "Bearer " . $access_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            $endpoint = $this->getPushEndpointWithDomain($subDomain);
            return $this->sendPushRequest($endpoint, $body, $headers);
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     * Send the request using the HTTP Client
     * @param $endpoint
     * @param $body
     * @param $headers
     * @return array
     * @throws \Exception
     */
    private function sendPushRequest($endpoint, $body, $headers)
    {
        try
        {
            $response = $this->client->post($endpoint, [
                'body' => json_encode($body),
                'headers' => $headers
            ]);
            if($response->getStatusCode() != '200')
            {
                throw new \Exception(json_decode($response->getBody()->getContents(), true), $response->getStatusCode());
            }
            return json_decode($response->getBody()->getContents(), true);
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    private function getPushEndpointWithDomain($subDomain)
    {
        return 'https://' . $subDomain . '.zendesk.com/api/v2/any_channel/push';
    }
}