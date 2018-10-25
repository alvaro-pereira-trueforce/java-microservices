<?php

namespace APIServices\LinkedIn\Services;

/**
 * Class LinkedinService
 * @package APIServices\LinkedIn\Services
 */
class LinkedinService
{
    /**
     * @var LinkedInAPI
     */
    protected $linkedinAPIClient;

    /**
     * LinkedinService constructor.
     * @param LinkedInAPI $linkedinAPIClient
     */
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

    /**
     * @param $message
     * @param $access_token
     * @return array
     * @throws \Exception
     */
    public function getAllCommentPost($message, $access_token)
    {
        try {
            $request_body = [
                'idCompany' => $message['updateContent']['company']['id'],
                'updateKey' => $updateKey = $message['updateKey'],
                'access_token' => $access_token
            ];
            return $this->linkedinAPIClient->getAllFromPost($request_body);

        } catch (\Exception $exception) {
            throw $exception;

        }
    }
}