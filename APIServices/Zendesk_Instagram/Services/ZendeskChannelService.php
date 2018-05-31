<?php

namespace APIServices\Zendesk_Instagram\Services;


use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk\Models\Formatters\Instagram\CommentFormatter;
use APIServices\Zendesk\Models\Formatters\Instagram\PostFormatter;
use APIServices\Zendesk\Models\Utils\Instagram\Comment;
use APIServices\Zendesk\Models\Utils\Instagram\ITransformer;
use APIServices\Zendesk\Models\Utils\Instagram\Post;
use APIServices\Zendesk\Models\Utils\Instagram\Reply;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ZendeskChannelService
{

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
    public function __construct(InstagramService $instagramService, $state = [])
    {
        $this->instagram_service = $instagramService;
        $this->chanel_type = 'INSTAGRAM';
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
        $transformedMessages = [];
        $post_timestamp = $this->state;
        $ownerPostEither = $this->instagram_service->getOwner();
        if ($ownerPostEither->isError()) {
            return $this->getResponsePull($transformedMessages, $post_timestamp);
        }
        $owner = $ownerPostEither->success();
        $postsEither = $this->instagram_service->getPosts(199);
        if ($postsEither->isError()) {
            return $this->getResponsePull($transformedMessages, $post_timestamp);
        }
        $posts = $postsEither->success();
        //It is done to start with the oldest post, to show properly in Zendes.
        $posts = array_reverse($posts, false);
        /** @varITransformer $transformer */
        $formatterPosts = App::makeWith(Post::class, [
            'owner' => $owner,
            'posts' => $posts,
            'state' => $this->state
        ]);
        $transformedPosts = $formatterPosts->generateToTransformedMessage();
        $transformedMessagesPosts = $transformedPosts['transformedMessages'];
        $post_timestamp = $transformedPosts['state'];
        $postIdToComments = $transformedPosts['postIdToComments'];

        $postsComments = $this->getCommentsFromPosts($owner, $postIdToComments);
        $formatterComments = App::makeWith(Comment::class, [
            'postsComments' => $postsComments
        ]);
        $transformedComments = $formatterComments->generateToTransformedMessage();
        $transformedMessagesComments = $transformedComments['transformedMessages'];
        Log::info('MERGING...........................................');
        $transformedMessages = array_merge($transformedMessagesPosts, $transformedMessagesComments);

        //transformedMessages

//        $formatterReplies = App::makeWith(Reply::class, [
//            'owner' => $owner,
//            'post' => $posts
//
//        ]);
//        $transformedReplies = $formatterReplies->getTransformedMessage();

        // get union
        // return

        return $this->getResponsePull($transformedMessages, $post_timestamp);
    }

    /**
     * @param $transformedMessages
     * @param $post_timestamp
     * @return array
     */
    private function getResponsePull($transformedMessages, $post_timestamp)
    {
        return [
            'external_resources' => $transformedMessages,
            'state' => json_encode(['last_post_date' => sprintf('%s', $post_timestamp)])
        ];
    }

    /**
     * @param $owner
     * @param $postIdToComments
     * @return array
     */
    private function getCommentsFromPosts($owner, $postIdToComments)
    {
        $toTransformerComments = [];
        foreach ($postIdToComments as $postId) {
            $responseComment = $this->instagram_service->getCommentsFromPost($postId);
            if ($responseComment->isSuccess()) {
                $comments = $responseComment->success();
                $commentsThread = [
                    'thread_id' => [
                        'user_id' => $owner['id'],
                        'post_id' => $postId,
                    ],
                    'comments' => $comments
                ];
                array_push($toTransformerComments, $commentsThread);
            }
        }
        return $toTransformerComments;
    }

    /**
     * @param $owner_post
     * @param $post
     * @return array|null
     */
    private function getUpdatesPosts($owner_post, $post)
    {
        try {
            /** @var PostFormatter $formatter */
            $formatter = App::makeWith($this->chanel_type . '.' . $post['media_type'], [
                'owner' => $owner_post,
                'post' => $post

            ]);
            return $formatter->getTransformedMessage();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param $owner_post
     * @param $post_id
     * @param $comment
     * @return array|null
     */
    private function getUpdatesComments($owner_post, $post_id, $comment)
    {
        try {
            /** @var CommentFormatter $formatter */
            $formatter = App::makeWith(CommentFormatter::class, [
                'thread_id' => [
                    'user_id' => $owner_post['id'],
                    'post_id' => $post_id,
                ],
                'comment' => $comment
            ]);
            return $formatter->getTransformedMessage();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param $post_id
     * @param $message
     * @return string
     * @throws \Exception
     */
    public function sendInstagramMessage($post_id, $message)
    {
        $commentEither = $this->instagram_service->sendInstagramMessage($post_id, $message);
        if ($commentEither->isError()) {
            throw new \Exception($commentEither->error()->getMessage());
        } else {
            return $commentEither->success();
        }
    }
}