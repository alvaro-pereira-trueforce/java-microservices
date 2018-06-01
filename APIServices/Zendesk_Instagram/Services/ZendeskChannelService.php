<?php

namespace APIServices\Zendesk_Instagram\Services;


use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk\Models\Utils\Instagram\Comment;
use APIServices\Zendesk\Models\Utils\Instagram\ITransformer;
use APIServices\Zendesk\Models\Utils\Instagram\Post;
use APIServices\Zendesk\Models\Utils\Instagram\Reply;
use APIServices\Commons\Util\Either;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ZendeskChannelService
{

    /**
     * @var InstagramService
     */
    protected $instagram_service;
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
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
        $transformedMessages = [];
        //TODO Posts
        $post_timestamp = $this->state;
        $postsFromOwner = $this->getPostFromOwner();
        if ($postsFromOwner->isError()) {
            return $this->getResponsePull($transformedMessages, $post_timestamp);
        }
        $postsOwner = $postsFromOwner->success();
        /** @var Post $post */
        $formatterPosts = App::makeWith(Post::class, [
            'owner' => $postsOwner['owner'],
            'posts' => $postsOwner['posts'],
            'state' => $this->state
        ]);
        $transformedPosts = $formatterPosts->generateToTransformerMessage();
        $transformedMessagesPosts = $transformedPosts['transformedMessages'];
        $post_timestamp = $transformedPosts['state'];
        //TODO Comments
        $postIdToComments = $transformedPosts['postIdToComments'];
        $postsComments = $this->getCommentsFromPosts($postsOwner['owner'], $postIdToComments);
        /** @var Comment $comment */
        $formatterComments = App::makeWith(Comment::class, [
            'postsComments' => $postsComments
        ]);
        $transformedComments = $formatterComments->generateToTransformerMessage();
        $transformedMessagesComments = $transformedComments['transformedMessages'];
        $transformedMessages = array_merge($transformedMessagesPosts, $transformedMessagesComments);
        //TODO Replies
        $dataForReplies = $transformedComments['dataForReplies'];
        $commentsReplies = $this->getRepliesFromComments($dataForReplies);
        /** @var Reply $reply */
        $formatterReplies = App::makeWith(Reply::class, [
            'commentsReplies' => $commentsReplies
        ]);
        $transformedReplies = $formatterReplies->generateToTransformerMessage();
        $transformedMessagesReplies = $transformedReplies['transformedMessages'];
        $transformedMessages = array_merge($transformedMessages, $transformedMessagesReplies);
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
     * @return Either|array
     */
    private function getPostFromOwner()
    {
        $ownerPostEither = $this->instagram_service->getOwner();
        if ($ownerPostEither->isError()) {
            return $ownerPostEither;
        }
        $postsEither = $this->instagram_service->getPosts(199);
        if ($postsEither->isError()) {
            return $postsEither;
        }
        $posts = $postsEither->success();
        //It is done to start with the oldest post, to show properly in Zendesk.
        $posts = array_reverse($posts, false);
        $postFromOwner = ['owner' => $ownerPostEither->success(),
            'posts' => $posts];
        return Either::successCreate($postFromOwner);
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
                $commentsData = [
                    'thread_id' => [
                        'user_id' => $owner['id'],
                        'post_id' => $postId,
                    ],
                    'comments' => $comments
                ];
                array_push($toTransformerComments, $commentsData);
            }
        }
        return $toTransformerComments;
    }

    /**
     * @param $dataForReplies
     * @return array
     */
    private function getRepliesFromComments($dataForReplies)
    {
        $toTransformerReplies = [];
        foreach ($dataForReplies as $dataForReply) {
            $responseReplies = $this->instagram_service->geRepliesFromComment($dataForReply['id']);
            if ($responseReplies->isSuccess()) {
                $replies = $responseReplies->success();
                //It is done to start with the oldest reply, to show properly in Zendesk.
                $replies = array_reverse($replies, false);
                $repliesData = [
                    'data_for_replies' => $dataForReply,
                    'replies' => $replies
                ];
                array_push($toTransformerReplies, $repliesData);
            }
        }
        return $toTransformerReplies;
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