<?php

namespace APIServices\Facebook\Models;


use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook as FB;
use Illuminate\Support\Facades\Log;

class Facebook extends FB
{

    protected $access_token;
    protected $instagram_id;
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
        parent::__construct($config);
        if ($access_token && $access_token != '') {
            /* // this is just an example do not set the state before instantiate it.
            $user = $this->get('/me');
            $user->getGraphUser();*/
            $this->setDefaultAccessToken($access_token);
            $this->access_token = $access_token;
            $this->instagram_id = $instagram_id;
            $this->page_id = $page_id;
        }
    }

    public function setInstagramID($instagram_id)
    {
        $this->instagram_id = $instagram_id;
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
            Log::debug($response);
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
     * @param string $endpoint
     * @return mixed
     * @throws FacebookSDKException
     */
    protected function deleteRequest($endpoint)
    {
        try {
            $response = json_decode($this->delete($endpoint)->getBody(), true);
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
     * @param $page_id
     * @return string
     * @throws \Exception
     */
    public function getPageAccessToken($page_id)
    {
        try {
            $response = $this->getRequest('/' . $page_id . '?fields=access_token,name');
            return $response['access_token'];
        } catch (\Exception $exception) {
            throw $exception;
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
            $url_post = '/' . $this->instagram_id . '/media?fields=id,media_type,caption,media_url,thumbnail_url,permalink,username,timestamp,comments_count&limit=' . $limit;
            return $this->getRequest($url_post);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param string $limitMedia
     * @param string $limitComments
     * @return array
     * @throws \Exception
     */
    public function getMediaWithComments($limitMedia = '', $limitComments = '')
    {
        try {
            $url_post = '/' . $this->instagram_id . '/media?fields=id,comments{id}';
            if (empty($limitMedia) && !empty($limitComments)) {
                $url_post = '/' . $this->instagram_id . '/media?fields=id,comments.limit(' . $limitComments . '){id,text}';
            }
            if (!empty($limitMedia) && empty($limitComments)) {
                $url_post = '/' . $this->instagram_id . '/media?fields=id,comments{id,text}&limit=' . $limitMedia;
            }
            if (!empty($limitMedia) && !empty($limitComments)) {
                $url_post = '/' . $this->instagram_id . '/media?fields=id,comments.limit(' . $limitComments . '){id,text}&limit=' . $limitMedia;
            }
            return $this->getRequest($url_post);
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
            $url_comments = '/' . $post_id . '/comments?fields=id,text,username,timestamp,replies{id,text,username,timestamp}&limit=' . $limit;
            return $this->getRequest($url_comments);
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
            $url_comment = '/' . $post_id . '/comments?message=' . $message;
            return $this->postRequest($url_comment);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param $page_id
     * @throws \Exception
     */
    public function setSubscribePageWebHooks($page_id)
    {
        try {
            $url = '/' . $page_id . '/subscribed_apps';
            $response = $this->postRequest($url);
            Log::debug('WebHook Registered:');
            Log::debug($response);
            if (!array_key_exists('success', $response))
                throw new \Exception($response);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param $page_id
     * @throws \Exception
     */
    public function deletePageSubscriptionWebhook($page_id)
    {
        try {
            $url = '/' . $page_id . '/subscribed_apps';
            $response = $this->deleteRequest($url);
            Log::debug('Webhook Deleted:');
            Log::debug($response);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get instagram Comment
     * @param $media_id
     * @return array
     * @throws \Exception
     */
    public function getInstagramMediaByID($media_id)
    {
        try {
            $url_post = '/' . $media_id . '?fields=id,comments_count,caption,like_count,media_type,media_url,permalink,username,is_comment_enabled,thumbnail_url,timestamp,owner,shortcode,ig_id,comments{id,text,username,like_count,timestamp,replies{id,text,username,like_count,timestamp,hidden,media,user},media}';
            return $this->getRequest($url_post);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * Get instagram Comment
     * @param $comment_id
     * @return array
     * @throws \Exception
     */
    public function getInstagramCommentByID($comment_id)
    {
        try {
            $url_post = '/' . $comment_id . '?fields=id,media,text,username,timestamp,hidden,like_count';
            return $this->getRequest($url_post);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * This will get only ids
     * @param $comment_id
     * @return array
     * @throws \Exception
     */
    public function getMediaWithCommentsAndReplies($media_id)
    {
        try {
            $url_post = '/' . $media_id . '?fields=comments{replies{id}}';
            return $this->getRequest($url_post);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }
}