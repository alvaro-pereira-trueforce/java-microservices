<?php

namespace APIServices\Instagram\Services;

use APIServices\Commons\Tools\TypeError;
use APIServices\Commons\Util\Either;
use APIServices\Commons\UtilError;
use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk_Instagram\Repositories\CommentTrackerRepository;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;


class InstagramService {
    protected $database;

    protected $dispatcher;

    protected $zendeskUtils;

    protected $facebookService;

    protected $commentTrackerRepository;

    /**
     * InstagramService constructor.
     * @param DatabaseManager $database
     * @param Dispatcher $dispatcher
     * @param Utility $zendeskUtils
     * @param FacebookService $facebookService
     * @param CommentTrackerRepository $commentTrackerRepository
     */
    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        Utility $zendeskUtils,
        FacebookService $facebookService,
        CommentTrackerRepository $commentTrackerRepository
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->zendeskUtils = $zendeskUtils;
        $this->facebookService = $facebookService;
        $this->commentTrackerRepository =$commentTrackerRepository;
    }

    /**
     * @return Either
     */
    public function getOwner() {
        try {
            $owner = $this->facebookService->getOwnerInstagram();
            return Either::successCreate($owner);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Either::errorCreate(new Error('Failed to get Instagram Owner data.',TypeError::SERVER_FACEBOOK_ERROR));
        }
    }

    /**
     * @param $limit
     * @return Either
     */
    public function getPosts($limit) {
        try {
            $responsePost= $this->facebookService->getPosts($limit);
            $posts = $responsePost['data'];
            return Either::successCreate($posts);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Either::errorCreate(new Error('Error getting the Instagram post.',TypeError::SERVER_FACEBOOK_ERROR));
        }
    }

    /**
     * @param $post_id
     * @param int $limit
     * @return Either
     */
    public function getCommentsFromPost($post_id, $limit=1000) {
        try {

            $responseComments = $this->facebookService->getComments($post_id,$limit);
            $comments = $responseComments['data'];
            return Either::successCreate($comments);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Either::errorCreate(new Error('Error getting the comments of the post.',TypeError::SERVER_FACEBOOK_ERROR));
        }
    }

    /**
     * @param $name
     * @param $token
     * @param $subdomain
     * @param $instagram_id
     * @param $page_id
     * @return string
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
     * @param $date
     * @return Either
     */
    public function commentTrack($post_id,$date){
        try {
            $commentTracker = $this->commentTrackerRepository->findByPostID($post_id);
            if($commentTracker==null){
                Log::info("Creating comment traker in repository");
                $commentTracker = $this->commentTrackerRepository->create(
                    [
                        'post_id'=> $post_id,
                        'last_comment_date' => $date
                    ]
                );
                return Either::successCreate($commentTracker);
            }
            return Either::successCreate($commentTracker);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Either::errorCreate(new Error('Failed to get comment history.',TypeError::SERVER_FACEBOOK_ERROR));
        }
    }

    /**
     * @param $post_id
     * @return Either
     */
    public function removePost($post_id){
        try {
            $commentTracker = $this->commentTrackerRepository->deleteByPostID($post_id);
            return Either::successCreate($commentTracker);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Either::errorCreate(new Error('Failed to delete comment history.',TypeError::SERVER_FACEBOOK_ERROR));
        }
    }

    /**
     * @param $post_id
     * @param $date
     * @return Either
     */
    public function updatePost($post_id, $date)
    {
        try {
            if ($post_id != null && $date != null) {
                $model = $this->commentTrackerRepository->findByPostID($post_id);
                $model->last_comment_date = $date;
                $model->save();
                return Either::successCreate($model->toArray());
            } else {
                $model = [
                    'post_id' => $post_id,
                    'last_comment_date' => $date
                ];
                return Either::successCreate($model);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Either::errorCreate(new Error('Error updating comment history.',TypeError::SERVER_FACEBOOK_ERROR));
        }
    }
}
