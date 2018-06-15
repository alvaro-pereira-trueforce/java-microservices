<?php

namespace APIServices\Zendesk\Models\Utils\Instagram;

use APIServices\Zendesk\Models\Formatters\Instagram\CommentFormatter;
use APIServices\Instagram\Services\InstagramService;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Comment implements ITransformer
{
    protected $postsComments;
    protected $instagramService;

    /**
     * Comment constructor.
     * @param $postsComments
     * @param InstagramService $instagramService
     */
    public function __construct($postsComments, InstagramService $instagramService)
    {
        $this->postsComments = $postsComments;
        $this->instagramService = $instagramService;
    }

    /**
     * @return array
     */
    public function generateToTransformerMessage()
    {
        $transformedMessages = [];
        $dataForReplies = [];
        foreach ($this->postsComments as $postComments) {
            $threadId = $postComments['thread_id'];
            $comments = $postComments['comments'];
            $lastCommentDate = null;
            foreach ($comments as $comment) {
                $commentTimestamp = date("c", strtotime($comment['timestamp']));
                $commentTimestamp = new Carbon($commentTimestamp);
                $commentTrackEither = $this->instagramService->commentTrack($threadId['post_id'], $commentTimestamp);
                if ($commentTrackEither->isSuccess()) {
                    $commentTrack = $commentTrackEither->success();
                    $lastCommentDate = $commentTrack->last_comment_date;
                    if ($commentTimestamp >= $lastCommentDate) {
                        $transformedComments = $this->getUpdatesComments($threadId, $comment);
                        if ($transformedComments != null) {
                            array_push($transformedMessages, $transformedComments);
                            $lastCommentDate = $commentTimestamp;
                            if (array_key_exists("replies", $comment)) {
                                array_push($dataForReplies, $this->generateDataForReply($threadId, $comment['id'], $comment['text']));
                            }
                        }
                    } else {
                        if (array_key_exists("replies", $comment)) {
                            array_push($dataForReplies, $this->generateDataForReply($threadId, $comment['id'], $comment['text']));
                        }
                    }
                }
            }
            //To update the date of the last comment
            $this->instagramService->updatePost($threadId['post_id'], $lastCommentDate);
        }
        return ['transformedMessages' => $transformedMessages, 'dataForReplies' => $dataForReplies];
    }

    /**
     * @param $threadId
     * @param $comment
     * @return array|null
     */
    private function getUpdatesComments($threadId, $comment)
    {
        try {
            /** @var CommentFormatter $formatter */
            $formatter = App::makeWith(CommentFormatter::class, [
                'thread_id' => $threadId,
                'comment' => $comment
            ]);
            return $formatter->getTransformedMessage();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Data for the thread id to create the reply
     *
     * @param $threadId
     * @param $commentId
     * @param $comment
     * @return array
     */
    private function generateDataForReply($threadId, $commentId, $comment)
    {
        return [
            'thread_id' => $threadId,
            'id' => $commentId,
            'comment' => $comment
        ];
    }
}