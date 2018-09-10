<?php

namespace APIServices\Services\LinkedIn;


use APIServices\LinkedIn\Services\LinkedinAPIClient;

class LinkedinService
{
    protected $linkedinAPIClient;

    public function __construct(LinkedinAPIClient $linkedinAPIClient)
    {
        $this->linkedinAPIClient = $linkedinAPIClient;
    }

    /**
     * Get Access Token is for your application to ask for one using the Authorization Code it just acquired.
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function getAuthorizationCode($params)
    {
        try {
            return $this->linkedinAPIClient->getAuthorizationCode($params);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}