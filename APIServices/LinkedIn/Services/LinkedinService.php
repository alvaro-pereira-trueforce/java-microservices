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
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getAuthorizationToken($params)
    {
        try {
            return $this->linkedinAPIClient->getAuthorizationToken($params);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getCompanies($params)
    {
        try {
            return $this->linkedinAPIClient->getCompanies($params);
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
     * @param $params
     * @param $access_token
     * @return array
     * @throws \Exception
     */
    public function getAllCommentPost($params, $access_token)
    {
        try {
            $request_body = [
                'idCompany' => $params['updateContent']['company']['id'],
                'updateKey' => $params['updateKey'],
                'access_token' => $access_token
            ];
            return $this->linkedinAPIClient->getAllFromPost($request_body);

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $params
     * @param $update_key
     * @return array
     * @throws \Exception
     */
    public function postLinkedInComment($params, $update_key)
    {
        try {
            $paramsBody = json_decode($params->metadata, true);
            $request_body = [
                'company_id' => $paramsBody['company_id'],
                'update_Key' => $update_key,
                'access_token' => $paramsBody['access_token'],
                'linkedinMessage' => $params['message']
            ];
            return $this->linkedinAPIClient->postCommentsCompany($request_body);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getLinkedInLikes($params)
    {
        try {
            $paramsGetLikes = explode(':', $params['thread_id']);
            $request_body = [
                'company_id' => $paramsGetLikes['1'],
                'update_Key' => $paramsGetLikes['2'],
                'access_token' => $params['access_token']
            ];
            return $this->linkedinAPIClient->getLikesLinkedInPost($request_body);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getLinkedInFollowers($params)
    {
        try {
            $paramsGetFollowers = explode(':', $params['thread_id']);
            $request_body = [
                'company_id' => $paramsGetFollowers['1'],
                'access_token' => $params['access_token']
            ];
            return $this->linkedinAPIClient->getFollowersLinkedInCompany($request_body);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getPostLinkedIn($params)
    {
        try {
            $paramsGetFollowers = explode(':', $params['thread_id']);
            $request_body = [
                'idCompany' => $paramsGetFollowers['1'],
                'updateKey' => $paramsGetFollowers['2'],
                'access_token' => $params['access_token']
            ];
            return $this->linkedinAPIClient->getAllFromPost($request_body);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
    public function getCommentsLinkedIn($params){
        try {
            $paramsGetFollowers = explode(':', $params['thread_id']);
            $request_body = [
                'idCompany' => $paramsGetFollowers['1'],
                'updateKey' => $paramsGetFollowers['2'],
                'access_token' => $params['access_token']
            ];
            return $this->linkedinAPIClient->getAllComment($request_body);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

}