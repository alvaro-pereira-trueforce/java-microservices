<?php

namespace APIServices\Facebook\Services;


use APIServices\Facebook\Models\Facebook;
use APIServices\Facebook\Repositories\FacebookRepository;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class FacebookService
{

    protected $api;
    protected $repository;

    public function __construct(Facebook $api, FacebookRepository $repository)
    {
        $this->api = $api;
        $this->repository = $repository;
    }

    /**
     * Get the access token for facebook endpoints using the code retrieved from the facebook authentication URL
     * @param $code
     * @return array
     * @throws \Exception
     */
    public function getAccessTokenFromFacebookCode($code)
    {
        try {
            /** @var Client $http_client */
            $http_client = App::make(Client::class);
            $response = $http_client->request('GET', 'https://graph.facebook.com/v3.0/oauth/access_token', [
                'query' => [
                    'client_id' => env('FACEBOOK_APP_ID'),
                    'redirect_uri' => env('APP_URL') . '/instagram/admin_ui',
                    'client_secret' => env('FACEBOOK_APP_SECRET'),
                    'code' => $code
                ]
            ]);
            $facebook_data = json_decode($response->getBody()->getContents(), true);
            if (array_key_exists('access_token', $facebook_data)) {
                return $facebook_data;
            } else {
                throw new \Exception("Facebook Bad Response: " . $facebook_data);
            }
        } catch (\Exception $exception) {
            throw $exception;
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
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
     * Get User pages
     *
     * @return array
     * @throws \Exception
     */
    public function getUserPages()
    {
        try {
            $pages = $this->api->getUserPages();
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
            throw new \Exception('The page does not have a linked instagram account. Please use the instagram application to link it to a facebook page.');
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
            throw new \Exception('This is not a valid Facebook page. Please create a valid Facebook page or contact support.');
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
    public function getInstagramMedia($token, $instagram_id, $limit)
    {
        try {
            $this->setAccessToken($token);
            return $this->api->getMedia($instagram_id, $limit);
        } catch (\Exception $exception) {
            throw new \Exception('The page does not have a linked instagram account. Please use the instagram application to link it to a facebook page. Media');
        }
    }

    /**
     * @param $token
     * @param $media_id
     * @param $limit
     * @return mixed
     * @throws \Exception
     */
    public function getInstagramComment($token, $media_id, $limit)
    {
        try {
            $this->setAccessToken($token);
            return $this->api->getComment($media_id, $limit);
        } catch (\Exception $exception) {
            throw new \Exception('The page does not have a linked instagram account. Please use the instagram application to link it to a facebook page. Comment');
        }
    }

    /**
     * @param $token
     * @param $media_id
     * @param $message
     * @return mixed
     * @throws \Exception
     */
    public function postInstagramComment($token, $media_id, $message)
    {
        try {
            $this->setAccessToken($token);
            return $this->api->postComment($media_id, $message);
        } catch (\Exception $exception) {
            throw new \Exception('The comment Error');
        }
    }

    /**
     * @param $page_id
     * @throws \Exception
     */
    public function setSubscribePageWebHooks($page_id)
    {
        try {
            $this->api->setSubscribePageWebHooks($page_id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw new \Exception('The page does not have a valid instagram account, Please use the instagram application to create a facebook page.');
        }
    }

    /**
     * @param $page_id
     * @param $page_access_token
     * @throws \Exception
     */
    public function deletePageSubscriptionWebhook($page_id, $page_access_token)
    {
        try {
            $this->api->setDefaultAccessToken($page_access_token);
            $this->api->deletePageSubscriptionWebhook($page_id);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}