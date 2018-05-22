<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Services\TicketService;
use Illuminate\Support\Facades\App;
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
    protected $message;

    protected $message_id;
    protected $user_id;
    protected $chat_id;
    protected $chat_type;
    protected $message_date;
    protected $user_username;
    protected $user_firstname;
    protected $user_lastname;
    protected $parent_id;
    protected $state;
    protected $telegramService;
    protected $ticketService;

    /**
     * MessageType constructor.
     *
     * @param Utility         $zendeskUtils
     * @param Update          $update
     * @param array           $state
     * @param TelegramService $telegramService
     * @param TicketService   $ticketService
     */
    public function __construct(TicketService $ticketService, Utility $zendeskUtils, $update, $state, TelegramService $telegramService) {
        $this->zendeskUtils = $zendeskUtils;
        $this->update = $update;
        $this->message = $update->getMessage();
        $this->message_id = $this->message->getMessageId();
        $this->user_id = $this->message->getFrom()->getId();
        $this->chat_id = $this->message->getChat()->getId();
        $this->chat_type = $this->message->getChat()->getType();
        $this->message_date = $this->message->getDate();
        $this->user_username = $this->message->getFrom()->getUsername();
        $this->user_firstname = $this->message->getFrom()->getFirstName();
        $this->user_lastname = $this->message->getFrom()->getLastName();
        $this->state = $state;
        $this->telegramService = $telegramService;
        $this->ticketService = $ticketService;
        $this->parent_id = $this->getParentID($this->message);
    }

    protected function getAuthorExternalID() {
        return $this->zendeskUtils->getExternalID([$this->user_id, $this->user_username]);
    }

    protected function getExternalID() {
        return $this->zendeskUtils->getExternalID([$this->parent_id, $this->message_id]);
    }

    protected function getAuthorName() {
        $author_name = $this->user_firstname;
        $user_name = '(' . $this->user_username . ')';
        if (!$this->user_lastname || trim($this->user_lastname) == '') {
            return $author_name . ' ' . $user_name;
        }

        return $author_name . ' ' . $this->user_lastname . ' ' . $user_name;
    }

    function getTransformedMessage() {
        return null;
    }


    /**
     * @param Message $message
     * @return string
     */
    protected function getParentID($message) {
        $reply = $message->getReplyToMessage();

        if ($reply) {
            $parent_id = $this->zendeskUtils->getExternalID([
                $reply->getChat()->get('id'),
                $reply->getFrom()->get('id')
            ]);
        } else {
            $parent_id = $this->zendeskUtils->getExternalID([
                $message->getChat()->getId(),
                $message->getFrom()->getId()
            ]);
        }
        $parent_uuid = $this->ticketService->getValidParentID($parent_id);
        return $this->zendeskUtils->getExternalID([$parent_uuid, $parent_id]);
    }
}