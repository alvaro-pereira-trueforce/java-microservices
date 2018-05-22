<?php

namespace APIServices\Zendesk_Instagram\Services;


use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk\Models\Formatters\Instagram\CommentFormatter;
use APIServices\Zendesk\Models\Formatters\Instagram\PostFormatter;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ZendeskChannelService {

    /**
     * @var InstagramService
     */
    protected $instagram_service;
    /**
     * @var string
     */
    protected $chanel_type;
    /**
     * @var array
     */
    protected $state;

    /**
     * ZendeskChannelService constructor.
     * @param InstagramService $instagramService
     * @param array $state
     */
    public function __construct(InstagramService $instagramService, $state = []) {
        $this->instagram_service = $instagramService;
        $this->chanel_type = 'INSTAGRAM';
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
        try {
            $owner_post = $this->instagram_service->getOwnerInstagram();
            $response = $this->instagram_service->getInstagramPosts(199);
            $posts = $response['data'];
            //It is done to start with the oldest post, to show properly in Zendes.
            $posts = array_reverse($posts, false);
            $post_timestamp = $this->state;
            Log::info("Printinf State");
            Log::debug($post_timestamp);
            Log::debug($posts);
            $transformedMessages = [];
            foreach ($posts as $post) {
                if (count($transformedMessages) > 195) {
                    break;
                }
                $post_id = $post['id'];
                $post_timestamp = date("c", strtotime($post['timestamp']));
                Log::info("PASO TIMESTAMP: " . $post_timestamp);
                if ($this->expire($post_timestamp)) {
                    Log::info("EXPIRED POST ID: " .$post_id .' DATE: '. $post_timestamp );
                    $this->instagram_service->removePost($post_id);
                    continue;
                }
                Log::info("PASO EXPIRE");
                if ($post_timestamp > $this->state) {
                    Log::info("THE POST ID: " .$post_id .' IS HIGHT WHAT: '. $this->state);
                    array_push($transformedMessages, $this->getUpdatesPosts($post));
                }
                Log::info("PASO CONDITION STATE");
                $response = $this->instagram_service->getInstagramCommentsFromPost($post_id);
                $comments = $response['data'];
                //It is done to start with the oldest post, to show properly in Zendes.
                $comments = array_reverse($comments, false);
                $last_comment_date = null;
                foreach ($comments as $comment) {
                    Log::info("HAVE COMMENT");
                    if (count($transformedMessages) > 199) {
                        break;
                    }

                    Log::debug($comment['timestamp']);
                    $comment_timestamp = date("c", strtotime($comment['timestamp']));
                    $comment_timestamp = new Carbon($comment_timestamp);
                    $comment_track = $this->instagram_service->commentTrack($post_id,$comment_timestamp);
                    $last_comment_date = $comment_track->last_comment_date;
                    Log::info("PREGUNTANDO: Coments:" .$comment_timestamp . " last" . $last_comment_date);
                    if ($comment_timestamp >= $last_comment_date) {
                        array_push($transformedMessages, $this->getUpdatesComments($owner_post, $post_id, $comment));
                    }
                }
                $this->instagram_service->updatePost($post_id, $last_comment_date);
            }
            //To Zendesk Pull
            $response = [
                'external_resources' => $transformedMessages,
                'state' => json_encode(['last_post_date' => sprintf('%s', $post_timestamp)])
            ];
            return $response;
        } catch (\Exception $exception) {
//            return [
//                'external_resources' => [],
//                'state' => '{}',
//                'metadata_needs_update' => true
//            ];
            return ['Message Error: ' + $exception->getMessage()];
        }
    }

    private function getUpdatesPosts($owner_post, $post)
    {
        /** @var PostFormatter $formatter */
        $formatter = App::makeWith($this->chanel_type . '.' . $post['media_type'], [
            'owner' => $owner_post,
            'post' => $post

        ]);
        return $formatter->getTransformedMessage();
    }

    private function getUpdatesComments($owner_post, $post_id, $comment)
    {
        /** @var CommentFormatter $formatter */
        $formatter = App::makeWith(CommentFormatter::class, [
            'thread_id' => [
                'user_id' => $owner_post['id'],
                'post_id' => $post_id,
            ],
            'comment' => $comment
        ]);
        return $formatter->getTransformedMessage();
    }

    private function expire($date)
    {
        Log::info("IN EXPIRE:" . $date);
        $date = new Carbon($date);
        $difference = $date->diffInMinutes(Carbon::now());
        Log::info("DIFERENCE: " . $difference);
        return $date->diffInMinutes(Carbon::now()) < (int)env('TIME_EXPIRE_FOR_TICKETS_IN_MINUTES_INSTAGRAM');
    }

    /**
     * @param $post_id
     * @param $message
     * @return string
     */
    public function sendInstagramMessage($post_id, $message) {
        try {
            $comment = $this->instagram_service->sendInstagramMessage($post_id,$message);
            return $comment['id'];
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "";
        }
    }
}