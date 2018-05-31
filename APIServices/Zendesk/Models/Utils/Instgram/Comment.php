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
    public function __construct($postsComments ,InstagramService $instagramService)
    {
        $this->postsComments = $postsComments;
        $this->instagramService = $instagramService;
    }

    public function generateToTransformedMessage()
    {
        $transformedMessages = [];
        $commentIdToReplies = [];
        Log::info("POst COMENTS");
        Log::debug($this->postsComments);
        foreach ($this->postsComments as $postComments) {
            $threadId = $postComments['thread_id'];
            $comments = $postComments['comments'];
            $lastCommentDate = null;
            Log::info("ANTEST DEL FOR");
            Log::debug($comments);
            foreach ($comments as $comment) {
                Log::info("ES un COMMENT");
                Log::debug($comment);
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
                            array_push($commentIdToReplies, $comment['id']);
                            $lastCommentDate = $commentTimestamp;
                        }

                    } else {
                        array_push($commentIdToReplies, $comment['id']);
                    }
                }
            }
            //To update the date of the last comment
            $this->instagramService->updatePost($threadId['post_id'], $lastCommentDate);
        }
        return ['transformedMessages' => $transformedMessages, 'commentIdToReplies' => $commentIdToReplies];
    }

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
}