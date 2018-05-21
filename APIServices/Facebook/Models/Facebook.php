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
     * @param array $state
     * @throws FacebookSDKException
     */
    public function __construct(array $config = [], ?$access_token = '', ?$instagram_id='',
                                ?$page_id='', ?$state = []) {
        try
        {
            if($access_token && $access_token!= '')
            {
                $this->setDefaultAccessToken($access_token);
                $user = $this->get('/me');
                $user->getGraphUser();
            }
            $this->state = $state;
            $this->access_token = $access_token;
            $this->instagram_id = $instagram_id;
            $this->page_id = $page_id;

            parent::__construct($config);
        }catch (FacebookSDKException $exception)
        {
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
            $response = $this->getRequest('/'.$page_id.'?fields=instagram_business_account');
            return $response['instagram_business_account']['id'];
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param $instagram_id
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getMedia($instagram_id,$limit=0) {
        try {
            $url_media = '/'.$instagram_id.'/media?fields=id,media_type,caption,media_url,thumbnail_url,permalink,username,timestamp,comments_count&limit=' . $limit;
            return $this->getRequest($url_media);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param $media_id
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getComment($media_id,$limit=0) {
        try {
            //17921734867137807/comments?fields=id,text,username,timestamp,replies{id,text,username,timestamp}&limit=10
            $url_comment = '/'.$media_id.'/comments?fields=id,text,username,timestamp,replies{id,text,username,timestamp}&limit='.$limit;
            return $this->getRequest($url_comment);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param $media_id
     * @param $message
     * @return mixed
     * @throws \Exception
     */
    public function postComment($media_id,$message) {
        try {
            //17921734867137807/comments?fields=id,text,username,timestamp,replies{id,text,username,timestamp}&limit=10
            $url_comment = '/'.$media_id.'/comments?message=' . $message;
            return $this->postRequest($url_comment);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }
}