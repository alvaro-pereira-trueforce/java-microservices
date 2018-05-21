<?php

namespace APIServices\Zendesk_Instagram\Services;


use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk\Models\Formatters\Instagram\CommentFormatter;
use APIServices\Zendesk\Models\Formatters\Instagram\PostFormatter;
use Illuminate\Support\Facades\App;

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
    public function getUpdates() {
        try
        {
            $posts = $this->instagram_service->getInstagramPosts(199);
            $transformedMessages = [];
            foreach ($posts as $post) {
                if (count($transformedMessages) > 195) {
                    break;
                }
                /** @var PostFormatter $formatter */
                $formatter = App::makeWith(PostFormatter::class,[
                    'post' => $post
                ]);
                $message =  $formatter->getTransformedMessage();

                if ($post['comments_count'] > 0) {
                    $comments = $this->instagram_service->getInstagramCommentsFromPost($post);
                    foreach ($comments as $comment) {
                        if (count($transformedMessages) > 195) {
                            break;
                        }
                        /** @var CommentFormatter $formatter */
                        $formatter = App::makeWith(CommentFormatter::class,[
                            'comment' => $comment,
                            'post_id' => $post['id']
                        ]);
                        $message =  $formatter->getTransformedMessage();
                    }
                }
            }
            return $transformedMessages;
        }catch (\Exception $exception){
            return [
                'external_resources' => [],
                'state' => '{}',
                'metadata_needs_update' => true
            ];
        }
    }
}