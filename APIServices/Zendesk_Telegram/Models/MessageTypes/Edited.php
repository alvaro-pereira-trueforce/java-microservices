<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use Carbon\Carbon;
use Telegram\Bot\Objects\Message;

class Edited extends EventMessageType
{
    function getTransformedMessage()
    {
        $edited_message = new Message($this->update->get('edited_message'));
        $this->message = $edited_message;
        $this->message_id = $this->message->getMessageId() . Carbon::now();
        $this->user_id = $this->message->getFrom()->getId();
        $this->chat_id = $this->message->getChat()->getId();
        $this->chat_type = $this->message->getChat()->getType();
        $this->message_date = $this->message->getDate();
        $this->user_username = $this->message->getFrom()->getUsername();
        $this->user_firstname = $this->message->getFrom()->getFirstName();
        $this->user_lastname = $this->message->getFrom()->getLastName();
        $this->parent_id = $this->getParentID($this->message);

        $message = $edited_message->getText();
        preg_match('/^\/([^\s@]+)@?(\S+)?\s?(.*)$/', $message, $commands);
        if (!empty($commands)) {
            return null;
        }
        $response = $this->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->parent_id,
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->getAuthorName()
        );

        $response = $this->zendeskUtils->addHtmlMessageToBasicResponse($response,
            view('telegram.edit_message', [
                'message' => $message,
            ])->render()
        );
        return $response;
    }
}