<?php

namespace APIServices\Zendesk_Telegram\Models;


use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Services\TicketService;
use Telegram\Bot\Objects\Message;

class TicketScaffold
{
    protected $zendeskUtils;
    protected $ticketService;

    public function __construct(Utility $zendeskUtils, TicketService $ticketService)
    {
        $this->zendeskUtils = $zendeskUtils;
        $this->ticketService = $ticketService;
    }

    public function getAuthorExternalID($user_id, $user_username)
    {
        return $this->zendeskUtils->getExternalID([$user_id, $user_username]);
    }

    public function getExternalID($parent_id, $message_id)
    {
        return $this->zendeskUtils->getExternalID([$parent_id, $message_id]);
    }

    public function getAuthorName($user_firstName, $user_username, $user_lastName)
    {
        $author_name = $user_firstName;
        $user_name = '(' . $user_username . ')';
        if (!$user_lastName || trim($user_lastName) == '') {
            return $author_name . ' ' . $user_name;
        }
        return $author_name . ' ' . $user_lastName . ' ' . $user_name;
    }

    /**
     * @param Message $message
     * @return string
     */
    public function getParentID($message) {
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