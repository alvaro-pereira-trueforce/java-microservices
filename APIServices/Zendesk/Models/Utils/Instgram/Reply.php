<?php

namespace APIServices\Zendesk\Models\Utils\Instagram;

use APIServices\Zendesk\Models\Formatters\Instagram\ReplyFormatter;
use APIServices\Instagram\Services\InstagramService;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class Reply implements ITransformer
{
    protected $commentsReplies;
    protected $instagramService;

    public function __construct($commentsReplies, InstagramService $instagramService)
    {
        $this->commentsReplies = $commentsReplies;
        $this->instagramService = $instagramService;
    }

    public function generateToTransformedMessage()
    {
        $transformedMessages = [];
        foreach ($this->commentsReplies as $replies) {
            $dataForReplies = $replies['data_for_replies'];
            $replies = $replies['replies'];
            $lastReplyDate = null;
            foreach ($replies as $reply) {
                $replyTimestamp = date("c", strtotime($reply['timestamp']));
                $replyTimestamp = new Carbon($replyTimestamp);
                $replyTrackEither = $this->instagramService->commentTrack($dataForReplies['id'], $replyTimestamp);
                if ($replyTrackEither->isSuccess()) {
                    $replyTrack = $replyTrackEither->success();
                    $lastReplyDate = $replyTrack->last_comment_date;
                    if ($replyTimestamp >= $lastReplyDate) {
                        $transformedReplies = $this->getUpdatesReplies($dataForReplies, $reply);
                        if ($transformedReplies != null) {
                            array_push($transformedMessages, $transformedReplies);
                            $lastReplyDate = $replyTimestamp;
                        }
                    }
                }
            }
            //To update the date of the last comment
            $this->instagramService->updatePost($dataForReplies['id'], $lastReplyDate);
        }
        return ['transformedMessages' => $transformedMessages];
    }

    private function getUpdatesReplies($dataForReply, $reply)
    {
        try {
            /** @var ReplyFormatter $formatter */
            $formatter = App::makeWith(ReplyFormatter::class, [
                'dataForReply' => $dataForReply,
                'reply' => $reply
            ]);
            return $formatter->getTransformedMessage();
        } catch (\Exception $exception) {
            return null;
        }
    }
}