<?php

namespace APIServices\Facebook\Services;


use APIServices\Facebook\Models\Facebook;
use APIServices\Facebook\Repositories\FacebookRepository;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Support\Facades\Log;

class FacebookService {

    protected $api;
    protected $repository;

    public function __construct(Facebook $api, FacebookRepository $repository) {
        $this->api = $api;
        $this->repository = $repository;
    }

    public function userHasPages($pages) {
        if (count($pages) == 0)
            return false;
        else
            return true;
    }

    /**
     * Get authentication token
     *
     * @var $cookie_string
     * @return string
     * @throws FacebookResponseException
     * @throws FacebookSDKException
     * @throws \Exception
     */
    public function getAuthentication($cookie_string) {
        $helper = $this->api->getLaravelScriptHelper($cookie_string);

        $accessToken = $helper->getAccessToken();
        $this->api->setDefaultAccessToken($accessToken);
        try {
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $response = $this->api->get('/me', '');
            $response->getGraphUser();
            return $response->getAccessToken();
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            Log::error('Graph returned an error: ' . $e->getMessage());
            throw $e;
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            Log::error('Facebook SDK returned an error: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $access_token
     */
    public function setAccessToken($access_token){
        $this->api->setDefaultAccessToken($access_token);
    }

    /**
     * Get AccessToken from user registration status
     * @param $uuid
     * @return string
     * @throws \Exception
     */
    public function getAccessTokenForNewRegistrationUser($uuid)
    {
        try
        {
            $model = $this->repository->getByUUID($uuid);
            $access_token = $model->facebook_token;
            $model->delete();
            return $access_token;
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }

        /**
     * Get User pages
     *
     * @return array
     * @throws \Exception
     */
    public function getUserPages() {
        try {
            $pages = $this->api->getUserPages();
            if (!$this->userHasPages($pages)) {
                throw new \Exception('User does not have pages. Please create a new business page and connect it to instagram business account.');
            }
            return $pages;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @var string $page_id
     * Get Instagram Accounts Connected to a Page
     * @return string
     * @throws \Exception
     */

    public function getInstagramAccountFromUserPage($page_id) {
        try
        {
            return $this->api->getPageInstagramID($page_id);
        }catch (\Exception $exception)
        {
            throw new \Exception('The page does not have an instagram account, Please use the instagram application to create a facebook page.');
        }
    }

    /**
     * @var string $page_id
     * Get Page Access Token
     * @return string
     * @throws \Exception
     */
    public function getPageAccessToken($page_id)
    {
        try {
            return $this->api->getPageAccessToken($page_id);
        } catch (\Exception $exception) {
            throw new \Exception('The page does not have an instagram account, Please use the instagram application to create a facebook page.');
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getOwnerInstagram()
    {
        try {
            return $this->api->getOwnerInstagram();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param $limit
     * @return mixed
     * @throws \Exception
     */
    public function getInstagramMedia($token,$instagram_id,$limit) {
        try
        {
            $this->setAccessToken($token);
            return $this->api->getMedia($instagram_id,$limit);
        }catch (\Exception $exception)
        {
            throw new \Exception('The page does not have an instagram account, Please use the instagram application to create a facebook page.');
        }
    }

    /**
     * @param $token
     * @param $media_id
     * @param $limit
     * @return mixed
     * @throws \Exception
     */
    public function getInstagramComment($token,$media_id,$limit) {
        try
        {
            $this->setAccessToken($token);
            return $this->api->getComment($media_id,$limit);
        }catch (\Exception $exception)
        {
            throw new \Exception('The Comment.');
        }
    }

    /**
     * @param $token
     * @param $media_id
     * @param $message
     * @return mixed
     * @throws \Exception
     */
    public function postInstagramComment($token,$media_id,$message) {
        try
        {
            $this->setAccessToken($token);
            return $this->api->postComment($media_id,$message);
        }catch (\Exception $exception)
        {
            throw new \Exception('The comment Error');
        }
    }

    /**
     * @param $page_id
     * @throws \Exception
     */
    public function setSubscribePageWebHooks($page_id){
        try
        {
            $this->api->setSubscribePageWebHooks($page_id);
        }catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
            throw new \Exception('The page does not have an instagram account, Please use the instagram application to create a facebook page.');
        }
    }

    /**
     * @param $page_id
     * @param $page_access_token
     * @throws \Exception
     */
    public function deletePageSubscriptionWebhook($page_id, $page_access_token)
    {
        try
        {
            $this->api->setDefaultAccessToken($page_access_token);
            $this->api->deletePageSubscriptionWebhook($page_id);
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }
}