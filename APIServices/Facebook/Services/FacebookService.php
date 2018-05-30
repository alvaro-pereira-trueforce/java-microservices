<?php

namespace APIServices\Facebook\Services;


use APIServices\Facebook\Models\Facebook;
use APIServices\Facebook\Repositories\FacebookRepository;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Support\Facades\Log;

class FacebookService
{
    /**
     * @var Facebook
     */
    protected $api;
    /**
     * @var FacebookRepository
     */
    protected $repository;

    /**
     * FacebookService constructor.
     * @param Facebook $api
     * @param FacebookRepository $repository
     */
    public function __construct(Facebook $api, FacebookRepository $repository)
    {
        $this->api = $api;
        $this->repository = $repository;
    }

    /**
     * @param $pages
     * @return bool
     */
    public function userHasPages($pages)
    {
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
    public function getAuthentication($cookie_string)
    {
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
    public function setAccessToken($access_token)
    {
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
        try {
            $model = $this->repository->getByUUID($uuid);
            $access_token = $model->facebook_token;
            $model->delete();
            return $access_token;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get User pages
     *
     * @return array
     * @throws \Exception
     */
    public function getUserPages()
    {
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
    public function getInstagramAccountFromUserPage($page_id)
    {
        try {
            return $this->api->getPageInstagramID($page_id);
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
     * @return array
     * @throws \Exception
     */
    public function getPosts($limit)
    {
        try {
            return $this->api->getPosts($limit);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param $post_id
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getComments($post_id, $limit = 1000)
    {
        try {
            return $this->api->getComments($post_id, $limit);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param $post_id
     * @param $message
     * @return mixed
     * @throws \Exception
     */
    public function sendMessage($post_id, $message)
    {
        try {
            return $this->api->postComment($post_id, $message);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }
}