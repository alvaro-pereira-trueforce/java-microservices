<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Zendesk\Utility;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

abstract class MessageType implements IMessageType {

    /**
     * @var Utility
     */
    protected $zendeskUtils;

    /**
     * @var Update
     */
    protected $update;
    /**
     * @var Message
     */
    protected  $message;

    protected  $message_id;
    protected  $user_id;
    protected  $chat_id;
    protected  $message_date;
    protected  $user_username;
    protected  $user_firstname;
    protected  $user_lastname;

    /**
     * MessageType constructor.
     *
     * @param Utility $zendeskUtils
     * @param Update  $update
     */
    public function __construct(Utility $zendeskUtils, $update) {
        $this->zendeskUtils = $zendeskUtils;
        $this->update = $update;
        $this->message = $update->getMessage();
        $this->message_id = $this->message->getMessageId();
        $this->user_id = $this->message->getFrom()->getId();
        $this->chat_id = $this->message->getChat()->getId();
        $this->message_date = $this->message->getDate();
        $this->user_username = $this->message->getFrom()->getUsername();
        $this->user_firstname = $this->message->getFrom()->getFirstName();
        $this->user_lastname = $this->message->getFrom()->getLastName();
    }

    protected function getParentID()
    {
        $reply = $this->message->getReplyToMessage();
        $parent_id = $this->zendeskUtils->getExternalID([$this->user_id, $this->chat_id]);
        if ($reply) {
            $parent_id = $this->zendeskUtils->getExternalID([$reply->getFrom()->get('id'), $reply->getChat()->get('id'), $reply->get('message_id')]);
        }
        return $parent_id;
    }

    protected function getExternalID()
    {
        return $this->zendeskUtils->getExternalID([$this->user_id, $this->chat_id, $this->message_id]);
    }

    protected function getAuthorExternalID()
    {
        return $this->zendeskUtils->getExternalID([$this->user_id, $this->user_username]);
    }
}