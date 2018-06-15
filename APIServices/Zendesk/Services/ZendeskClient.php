<?php

namespace APIServices\Zendesk\Services;


use GuzzleHttp\Client;

class ZendeskClient
{
    protected $client;
    public $access_token;

    public function __construct($access_token, Client $client)
    {
        $this->client = $client;
        $this->access_token = $access_token;
    }

    /**
     * Send the request using the HTTP Client
     * @param $endpoint
     * @param $body
     * @return array
     * @throws \Exception
     */
    public function sendRequest($endpoint, $body)
    {
        try
        {
            $headers = [
                'Authorization' => "Bearer " . $this->access_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

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

    /**
     * Get Basic Endpoint
     * @param $subDomain
     * @return string
     */
    public function getBasicEndpointWithSubDomain($subDomain)
    {
        return 'https://' . $subDomain . '.zendesk.com/api/v2/';
    }
}