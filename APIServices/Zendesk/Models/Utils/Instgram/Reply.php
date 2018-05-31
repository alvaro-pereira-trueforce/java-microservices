<?php

namespace APIServices\Zendesk\Models\Utils\Instagram;

use Carbon\Carbon;

class Reply implements ITransformer
{
    protected $commentReplies;
    protected $instagramService;

    public function __construct($commentReplies, InstagramService $instagramService)
    {
        $this->commentReplies = $commentReplies;
        $this->instagramService = $instagramService;
    }

    public function generateToTransformedMessage()
    {
        $transformedMessages = [];
        foreach ($this->commentReplies as $replies) {
            $commentId = $replies['id'];
            $threadId = $replies['thread_id'];
            foreach ($replies as $reply) {
                $replyTimestamp = date("c", strtotime($reply['timestamp']));
                $replyTimestamp = new Carbon($replyTimestamp);
                $replyTrackEither = $this->instagram_service->commentTrack($commentId, $replyTimestamp);
                if ($replyTrackEither->isSuccess()) {
                    $replyTrack = $replyTrackEither->success();
                    $lastReplyDate = $replyTrack->last_comment_date;
                    if ($replyTimestamp >= $lastReplyDate) {
                        $transformedReplies = $this->getUpdatesReplies($threadId, $reply);
                        if ($transformedReplies != null) {
                            array_push($transformedMessages, $transformedReplies);
                            $lastReplyDate = $replyTimestamp;
                        }
                    }
                }
            }
            //To update the date of the last comment
            $this->instagram_service->updatePost($commentId, $lastReplyDate);
        }
        return ['transformedMessages' => $transformedMessages];
    }
}