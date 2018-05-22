<?php

namespace APIServices\Zendesk_Instagram\Services;


use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk\Models\Formatters\Instagram\CommentFormatter;
use APIServices\Zendesk\Models\Formatters\Instagram\PostFormatter;
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
    public function __construct(InstagramService $instagramService,$state = []) {
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
            $transformedMessages = [];
            foreach ($posts as $post) {
                if (count($transformedMessages) > 195) {
                    break;
                }
                $post_timestamp = date("c", strtotime($post['timestamp']));
                if ($this->expire($post_timestamp)) {
                    if ($post_timestamp > $this->state) {
                        array_push($transformedMessages, $this->getUpdatesPosts($post));
                        if ($post['comments_count'] > 0) {
                            $post_id = $post['id'];
                            $response = $this->instagram_service->getInstagramCommentsFromPost($post_id);
                            $comments = $response['data'];
                            //It is done to start with the oldest post, to show properly in Zendes.
                            $comments = array_reverse($comments, false);
                            foreach ($comments as $comment) {
                                if (count($transformedMessages) > 199) {
                                    break;
                                }
                                $comment_timestamp = date("c", strtotime($comment['timestamp']));
                                $last_comment_date = getDate($post_id);
                                if ($comment_timestamp > $last_comment_date) {
                                    array_push($transformedMessages, $this->getUpdatesComments($owner_post, $post_id, $comment));
                                }
                            }
                        }
                    }
                } else {
                    deletePost($post['id']);
                }
            }
            Log::debug($transformedMessages);
            return $transformedMessages;
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