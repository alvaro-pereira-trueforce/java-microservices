<?php

namespace APIServices\LinkedIn\Services;

class LinkedinService
{
    protected $linkedinAPIClient;

    public function __construct(LinkedInAPI $linkedinAPIClient)
    {
        $this->linkedinAPIClient = $linkedinAPIClient;
    }

    /**
     * Get Access Token is for your application to ask for one using the Authorization Code it just acquired.
     * @param $code
     * @return array
     * @throws \Exception
     */
    public function getAuthorizationToken($code)
    {
        try {
            return $this->linkedinAPIClient->getAuthorizationToken($code);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param array
     * @return array
     * @throws \Exception
     */
    public function getCompanies($token_access)
    {
        try {
            return $this->linkedinAPIClient->getCompanies($token_access);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param array
     * @return array
     * @throws \Exception
     */
    public function getUpdates($params)
    {
        try {
            return $this->linkedinAPIClient->getAllUpdates($params);

        } catch (\Exception $exception) {
            throw $exception;

        }
    }
}