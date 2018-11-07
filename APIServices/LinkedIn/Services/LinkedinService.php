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
                'updateKey' => $updateKey = $params['updateKey'],
                'access_token' => $access_token
            ];
            return $this->linkedinAPIClient->getAllFromPost($request_body);

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getAllPostChannelBackFormat($params)
    {
        try {
            $paramsMetadata = json_decode($params->metadata, true);
            $paramsGetPost = explode(':', $params->thread_id);
            $request_body = [
                'idCompany' => $paramsGetPost['1'],
                'updateKey' => $paramsGetPost['2'],
                'access_token' => $paramsMetadata['access_token']
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
     * @param $metadata
     * @return array
     * @throws \Exception
     */
    public function getLinkedInLikes($metadata, $params)
    {
        try {
            $paramsGetLikes = explode(':', $params);
            $request_body = [
                'company_id' => $paramsGetLikes['1'],
                'update_Key' => $paramsGetLikes['2'],
                'access_token' => $metadata['access_token']
            ];
            return $this->linkedinAPIClient->getLikesCompany($request_body);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    /**
     * @param $params
     * @param $metadata
     * @return array
     * @throws \Exception
     */
    public function getLinkedInFollowers($metadata, $params)
    {
        try {
            $paramsGetFollowers = explode(':', $params);
            $request_body = [
                'company_id' => $paramsGetFollowers['1'],
                'access_token' => $metadata['access_token']
            ];
            return $this->linkedinAPIClient->getFollowersCompany($request_body);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    /**
     * @param $metadata
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function getPostLinkedIn($metadata, $params)
    {
        try {
            $paramsGetFollowers = explode(':', $params);
            $request_body = [
                'idCompany' => $paramsGetFollowers['1'],
                'updateKey'=>$paramsGetFollowers['2'],
                'access_token' => $metadata['access_token']
            ];
            return $this->linkedinAPIClient->getAllFromPost($request_body);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}