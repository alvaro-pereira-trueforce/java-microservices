<?php

namespace APIServices\Instagram\Services;

use APIServices\Zendesk_Instagram\Repositories\CommentTrackerRepository;
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

    protected $comment_tracker_repository;

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
        Facebook $facebookAPI,
        CommentTrackerRepository $comment_tracker_repository
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->zendeskUtils = $zendeskUtils;
        $this->facebookAPI = $facebookAPI;
        $this->comment_tracker_repository =$comment_tracker_repository;
    }

    /**
     * @return array
     * @throws \Exception
     */
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

    /**
     * @param $post_id
     * @param $message
     * @return mixed|string
     */
    public function sendInstagramMessage($post_id,$message) {
        try {
            return $this->facebookAPI->postComment($post_id,$message);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "";
        }
    }

    /**
     * @param $post_id
     * @return CommentTrack|string
     */
    public function commentTrack($post_id,$date){
        try {
            $comment_tracker = $this->comment_tracker_repository->findByPostID($post_id);
            if($comment_tracker==null){
                Log::info("NULL: " .$post_id);
                return $this->comment_tracker_repository->create(
                    [
                        'post_id'=> $post_id,
                        'last_comment_date' => $date
                    ]
                );
            }
            return $comment_tracker;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "Not Exist the comment track.";
        }
    }

    /**
     * @param $post_id
     * @return bool|string
     */
    public function removePost($post_id){
        try {
            return $this->comment_tracker_repository->deleteByPostID($post_id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "Not Exist the comment track.";
        }
    }

    /**
     * @param $post_id
     * @param $date
     * @return bool|string
     */
    public function updatePost($post_id, $date)
    {
        try {
            if ($post_id != null && $date != null) {
                $model = $this->comment_tracker_repository->findByPostID($post_id);
                $model->last_comment_date = $date;
                $model->save();
                return $model->toArray();
            } else {
                return [
                    'post_id' => $post_id,
                    'last_comment_date' => $date
                ];
            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "Not Exist the comment track.";
        }
    }
}
