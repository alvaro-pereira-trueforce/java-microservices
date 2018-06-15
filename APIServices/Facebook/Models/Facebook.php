<?php

namespace APIServices\Facebook\Models;


use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook as FB;
use Illuminate\Support\Facades\Log;

class Facebook extends FB
{
    /**
     * @var string
     */
    protected $access_token;
    /**
     * @var string
     */
    protected $instagram_id;
    /**
     * @var string
     */
    protected $page_id;

    /**
     * Facebook constructor.
     *
     * @param array $config
     * @param string $access_token
     * @param string $instagram_id
     * @param string $page_id
     * @throws FacebookSDKException
     */
    public function __construct(array $config = [], $access_token = '', $instagram_id = '',
                                $page_id = '')
    {
        try {
            parent::__construct($config);
            if ($access_token && $access_token != '') {
                $this->setDefaultAccessToken($access_token);
                $user = $this->get('/me');
                $user->getGraphUser();
                $this->access_token = $access_token;
                $this->instagram_id = $instagram_id;
                $this->page_id = $page_id;
            }


        } catch (FacebookSDKException $exception) {
            throw $exception;
        }
    }

    /**
     * @var $cookie_string
     * @return FacebookLaravelScriptHelper
     */
    function getLaravelScriptHelper($cookie_string)
    {
        return new FacebookLaravelScriptHelper($this->app, $this->client,
            $this->defaultGraphVersion, $cookie_string);
    }

    /**
     * @param string $endpoint
     * @return array
     * @throws FacebookSDKException
     */
    protected function getRequest($endpoint)
    {
        try {
            $response = json_decode($this->get($endpoint)->getBody(), true);
            return $response;
        } catch (FacebookSDKException $exception) {
            throw $exception;
        }
    }

    /**
     * @param string $endpoint
     * @return mixed
     * @throws FacebookSDKException
     */
    protected function postRequest($endpoint)
    {
        try {
            $response = json_decode($this->post($endpoint)->getBody(), true);
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
    public function getUserPages()
    {
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
    public function getPageInstagramID($page_id)
    {
        try {
            $response = $this->getRequest('/' . $page_id . '?fields=instagram_business_account');
            return $response['instagram_business_account']['id'];
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getOwnerInstagram()
    {
        try {
            $url_get_owner = '/' . $this->instagram_id . '?fields=id,name,username,profile_picture_url';
            return $this->getRequest($url_get_owner);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getPosts($limit = 1000)
    {
        try {
            $url = '/' . $this->instagram_id . '/media?fields=id,media_type,caption,media_url,thumbnail_url,permalink,username,timestamp,comments_count&limit=' . $limit;
            return $this->getRequest($url);
        } catch (\Exception $exception) {
            throw  $exception;
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
            $url = '/' . $post_id . '/comments?fields=id,text,username,timestamp,replies';
            return $this->getRequest($url);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param $post_id
     * @param $message
     * @return mixed
     * @throws \Exception
     */
    public function postComment($post_id, $message)
    {
        try {
            $url = '/' . $post_id . '/comments?message=' . $message;
            return $this->postRequest($url);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param $comment_id
     * @param int $limit
     * @return mixed
     * @throws \Exception
     */
    public function getReplies($comment_id, $limit=1000)
    {
        try {
            $url = '/' . $comment_id . '?fields=replies.limit('.$limit.'){id,text,username,timestamp}';
            return $this->getRequest($url);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }
}