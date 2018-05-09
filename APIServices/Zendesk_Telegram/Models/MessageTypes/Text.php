<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Text extends MessageType {

    function getTransformedMessage() {

        $message = $this->message->getText();

        preg_match('/^\/([^\s@]+)@?(\S+)?\s?(.*)$/', $message, $commands);
        if (!empty($commands)) {
            return null;
        }

        $response = $this->zendeskUtils->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->parent_id,
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->user_firstname . ' ' . $this->user_lastname);

        $reply = $this->message->getReplyToMessage();
        if($reply)
        {
            $response = $this->zendeskUtils->addHtmlMessageToBasicResponse($response,
                view('telegram.replay_message', [
                    'message' => $message,
                    'reply_text' => $reply->getText()
                ])->render()
            );
        }
        return $response;
    }
}