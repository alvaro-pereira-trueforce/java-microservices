<?php

namespace APIServices\LinkedIn\Services;


use Telegram\Bot\HttpClients\GuzzleHttpClient;

class LinkedinAPIClient
{
    protected $httpClient;
    protected $endpoints = [
        'GetAccessToken' => 'https://www.linkedin.com/oauth/v2/accessToken'
    ];

    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param array $params
     *
     * 'grant_type'    The value of this field should always be:  authorization_code Yes
     * 'code'    The authorization code you received from Step 2. Yes
     * 'redirect_uri'    The same 'redirect_uri' value that you passed in the previous step. Yes
     * 'client_id'    The "API Key" value generated Step 1. Yes
     * 'client_secret' The "Secret Key" value generated in Step 1. Yes
     *
     * @return string
     * @throws \Exception
     */
    public function getAuthorizationCode(array $params)
    {
        try {
            return "";
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}