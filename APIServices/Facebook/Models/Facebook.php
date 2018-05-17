<?php

namespace APIServices\Facebook\Models;


use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook as FB;

class Facebook extends FB {

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
            $response = $this->getRequest('/'.$page_id.'?fields=instagram_business_account');
            return $response['instagram_business_account']['id'];
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }
}