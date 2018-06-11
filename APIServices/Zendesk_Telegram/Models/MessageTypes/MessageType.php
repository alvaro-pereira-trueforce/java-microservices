<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Models\TicketScaffold;
use APIServices\Zendesk_Telegram\Services\TicketService;
use Illuminate\Support\Facades\App;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

abstract class MessageType implements IMessageType
{

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
    /** @var TicketScaffold $ticketScaffold */
    protected $ticketScaffold;

    /**
     * MessageType constructor.
     * @param Utility $zendeskUtils
     * @param Update $update
     * @param array $state
     * @param TelegramService $telegramService
     * @param TicketService $ticketService
     */
    public function __construct(TicketService $ticketService, Utility $zendeskUtils, $update, $state, TelegramService $telegramService)
    {
        $this->ticketScaffold = App::makeWith(TicketScaffold::class, [
            'zendeskUtils' => $zendeskUtils,
            'ticketService' => $ticketService
        ]);
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

    protected function getAuthorExternalID()
    {
        return $this->ticketScaffold->getAuthorExternalID($this->user_id, $this->user_username);
    }

    protected function getExternalID()
    {
        return $this->ticketScaffold->getExternalID($this->parent_id, $this->message_id);
    }

    protected function getAuthorName()
    {
        return $this->ticketScaffold->getAuthorName($this->user_firstname, $this->user_username, $this->user_lastname);
    }

    /**
     * @param Message $message
     * @return string
     */
    protected function getParentID($message)
    {
        return $this->ticketScaffold->getParentID($message);
    }
}