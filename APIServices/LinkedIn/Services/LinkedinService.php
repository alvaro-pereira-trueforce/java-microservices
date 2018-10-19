<?php

namespace APIServices\LinkedIn\Services;

use Illuminate\Support\Facades\Log;

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
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'Authorization failed');
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
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'Get companies List failed');
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
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'Get All the updated failed');

        }
    }

    /**
     * @param $message
     * @param $access_token
     * @return mixed
     */
    public function getAllCommentPost($message, $access_token)
    {
        try {
           // Log::debug($message);
            $response = [
                'idCompany' => $message['updateContent']['company']['id'],
                'updateKey' => $updateKey = $message['updateKey'],
                'access_token' => $access_token
            ];
            return $this->linkedinAPIClient->getAllFromPost($response);

        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'Get especific post comments failed');

        }
    }
}