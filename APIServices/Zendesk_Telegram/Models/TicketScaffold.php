<?php

namespace APIServices\Zendesk_Telegram\Models;


use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Services\TicketService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;

class TicketScaffold
{
    protected $zendeskUtils;
    protected $ticketService;
    protected $ticketSettings;

    public function __construct(Utility $zendeskUtils, TicketService $ticketService, $ticketSettings)
    {
        $this->zendeskUtils = $zendeskUtils;
        $this->ticketService = $ticketService;
        $this->ticketSettings = $ticketSettings;
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
    public function getParentID($message)
    {
        //This commented code create new tickets on reply
        //$reply = $message->getReplyToMessage();

//        if ($reply) {
//            $parent_id = $this->zendeskUtils->getExternalID([
//                $reply->getChat()->get('id'),
//                $reply->getFrom()->get('id')
//            ]);
//        } else {
        try {
            /** @var Api $api */
            $api = App::make(Api::class);
            $bot_id = $api->getMe()->getId();
        } catch (\Exception $exception) {
            $bot_id = $message->getChat()->getId();
        }

        if (
            array_key_exists('tickets_by_group', $this->ticketSettings) &&
            (bool)$this->ticketSettings['tickets_by_group'] == true &&
            ($message->getChat()->getType() == 'group' || $message->getChat()->getType() == 'supergroup')) {

            $parent_id = $this->zendeskUtils->getExternalID([
                $bot_id,
                $message->getChat()->getId()
            ]);
        } else {
            $parent_id = $this->zendeskUtils->getExternalID([
                $bot_id,
                $message->getChat()->getId(),
                $message->getFrom()->getId()
            ]);
        }
        //}
        $parent_uuid = $this->ticketService->getValidParentID($parent_id);
        return $this->zendeskUtils->getExternalID([$parent_uuid, $parent_id]);
    }
}