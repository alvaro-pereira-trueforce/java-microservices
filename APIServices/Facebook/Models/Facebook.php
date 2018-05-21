<?php

namespace APIServices\Facebook\Models;


use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook as FB;
use Illuminate\Support\Facades\Log;

class Facebook extends FB {

    protected $access_token;
    protected $instagram_id;
    protected $page_id;
    protected $state;

    /**
     * Facebook constructor.
     *
     * @param array  $config
     * @param string $access_token
     * @param string $instagram_id
     * @param string $page_id
     * @param array  $state
     * @throws FacebookSDKException
     */
    public function __construct(array $config = [], $access_token = '', $instagram_id = '',
                                $page_id = '', $state = []) {
        try {
            if ($access_token && $access_token != '') {
                $this->setDefaultAccessToken($access_token);
                $user = $this->get('/me');
                $user->getGraphUser();
                $this->state = $state;
                $this->access_token = $access_token;
                $this->instagram_id = $instagram_id;
                $this->page_id = $page_id;
            }

            parent::__construct($config);
        } catch (FacebookSDKException $exception) {
            throw $exception;
        }
    }

    /**
     * @var $cookie_string
     * @return FacebookLaravelScriptHelper
     */
    function getLaravelScriptHelper($cookie_string) {
        return new FacebookLaravelScriptHelper($this->app, $this->client,
            $this->defaultGraphVersion, $cookie_string);
    }

    /**
     * @param string $endpoint
     * @return array
     * @throws FacebookSDKException
     */
    protected function getRequest($endpoint) {
        try {
            $response = json_decode($this->get($endpoint)->getBody(), true);
            return $response;
        } catch (FacebookSDKException $exception) {
            throw $exception;
        }
    }

    /**
     * Get User Pages
     *
     * @return array
     * @throws FacebookSDKException
     */
    public function getUserPages() {
        try {
            $response = $this->getRequest('/me/accounts');
            if (array_key_exists('data', $response)) {
                return $response['data'];
            }
            return [];
        } catch (FacebookSDKException $exception) {
            throw $exception;
        }
    }

    /**
     * @param $page_id
     * @return string
     * @throws \Exception
     */
    public function getPageInstagramID($page_id) {
        try {
            $response = $this->getRequest('/' . $page_id . '?fields=instagram_business_account');
            return $response['instagram_business_account']['id'];
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }
}