<?php

namespace APIServices\Instagram\Services;

use APIServices\Facebook\Models\Facebook;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;


class InstagramService {
    protected $database;

    protected $dispatcher;

    protected $zendeskUtils;

    protected $facebookAPI;

    /**
     * InstagramService constructor.
     * @param DatabaseManager $database
     * @param Dispatcher $dispatcher
     * @param Utility $zendeskUtils
     * @param Facebook $facebookAPI
     */
    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        Utility $zendeskUtils,
        Facebook $facebookAPI
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->zendeskUtils = $zendeskUtils;
        $this->facebookAPI = $facebookAPI;
    }

    public function getOwnerInstagram() {
        try {
            return $this->facebookAPI->getOwnerInstagram();
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
    public function getInstagramPosts($limit) {
        try {
            return $this->facebookAPI->getPosts($limit);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param $post_id
     * @param $limit
     * @return array
     */
    public function getInstagramCommentsFromPost($post_id, $limit=1000) {
        try {
            return $this->facebookAPI->getComments($post_id,$limit);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }

    /**
     * @param $name
     * @param $token
     * @param $subdomain
     * @param $instagram_id
     * @param $page_id
     * @return array|null
     */
    public function registerNewIntegration($name, $token, $subdomain, $instagram_id, $page_id) {
        return json_encode([
            'integration_name' => $name,
            'zendesk_app_id' => $subdomain,
            'token' => $token,
            'instagram_id' => $instagram_id,
            'page_id' => $page_id
        ]);
    }

    public function sendInstagramMessage($post_id,$message) {
        try {
            return $this->facebookAPI->postComment($post_id,$message);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "";
        }
    }
}
