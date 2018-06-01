<?php

namespace APIServices\Zendesk\Models\Formatters\Instagram;

use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;

class ReplyFormatter extends CommentFormatter
{

    protected $commentReply;

    public function __construct($dataForReply, $reply, Utility $utility)
    {
        parent::__construct($dataForReply['thread_id'], $reply, $utility);
        $this->commentReply = $dataForReply['comment'];
    }

    function getTransformedMessage()
    {
        try {
            $transformedMessages = parent::getTransformedMessage();
            return $this->utility->addHtmlMessageToBasicResponse($transformedMessages,
                view('instagram.multimedia.reply_viewer', [
                    'reply_text' => $this->commentReply
                ])->render()
            );
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}