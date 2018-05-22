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
     * ZendeskChannelService constructor.
     * @param InstagramService $instagramService
     */
    public function __construct(InstagramService $instagramService) {
        $this->instagram_service = $instagramService;
        $this->chanel_type = 'INSTAGRAM';
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
                /** @var PostFormatter $formatter */
                $formatter = App::makeWith($this->chanel_type . '.' . $post['media_type'], [
                    'owner' => $owner_post,
                    'post' => $post

                ]);
                $message = $formatter->getTransformedMessage();
                array_push($transformedMessages, $message);

                $post_id = $post['id'];
                if ($post['comments_count'] > 0) {
                    $response = $this->instagram_service->getInstagramCommentsFromPost($post_id);
                    $comments = $response['data'];
                    //It is done to start with the oldest post, to show properly in Zendes.
                    $comments = array_reverse($comments, false);
                    Log::debug($comments);
                    foreach ($comments as $comment) {
                        if (count($transformedMessages) > 195) {
                            break;
                        }
                        /** @var CommentFormatter $formatter */
                        $formatter = App::makeWith(CommentFormatter::class, [
                            'thread_id' => [
                                'user_id'=>$owner_post['id'],
                                'post_id'=>$post_id,
                            ],
                            'comment' => $comment
                        ]);
                        $message = $formatter->getTransformedMessage();
                        array_push($transformedMessages, $message);
                    }
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