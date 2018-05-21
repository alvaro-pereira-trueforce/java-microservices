<?php

namespace APIServices\Zendesk_Instagram\Services;


use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk\Models\Formatters\Instagram\CommentFormatter;
use APIServices\Zendesk\Models\Formatters\Instagram\PostFormatter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ZendeskChannelService {

    protected $instagram_service;
    protected $chanel_type;

    public function __construct(InstagramService $instagramService) {
        $this->instagram_service = $instagramService;
        $this->chanel_type = 'instagram';
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
        try {
            $response = $this->instagram_service->getInstagramPosts(199);
            $posts = $response['data'];
            Log::info($posts);
            //It is done to start with the oldest post, to show properly in Zendes.
            $posts = array_reverse($posts, true);
            Log::debug($posts);
            $transformedMessages = [];
            foreach ($posts as $post) {
                if (count($transformedMessages) > 195) {
                    break;
                }
                /** @var PostFormatter $formatter */
                $formatter = App::makeWith(PostFormatter::class, [
                    'post' => $post
                ]);
                $message = $formatter->getTransformedMessage();
                array_push($transformedMessages, $message);
                $post_id = $post['id'];
                if ($post['comments_count'] > 0) {
                    $comments = $this->instagram_service->getInstagramCommentsFromPost($post_id);
                    //It is done to start with the oldest post, to show properly in Zendes.
                    $comments = array_reverse($comments, true);
                    foreach ($comments as $comment) {
                        if (count($transformedMessages) > 195) {
                            break;
                        }
                        /** @var CommentFormatter $formatter */
                        $formatter = App::makeWith(CommentFormatter::class, [
                            'comment' => $comment,
                            'post_id' => $post_id
                        ]);
                        $message = $formatter->getTransformedMessage();
                        array_push($transformedMessages, $message);
                    }
                }
            }
            return $posts;
        } catch (\Exception $exception) {
            return [
                'external_resources' => [],
                'state' => '{}',
                'metadata_needs_update' => true
            ];
        }
    }
}