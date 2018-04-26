<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Zendesk\Utility;

class Text implements IMessageType {

    protected $zendeskUtils;

    public function __construct(Utility $zendeskUtils) {
        $this->zendeskUtils = $zendeskUtils;
    }

    function getTransformedMessage($update) {
        $message_id = $update->getMessage()->getMessageId();
        $user_id = $update->getMessage()->getFrom()->getId();
        $chat_id = $update->getMessage()->getChat()->getId();
        $message_date = $update->getMessage()->getDate();
        $user_username = $update->getMessage()->getFrom()->getUsername();
        $user_firstname = $update->getMessage()->getFrom()->getFirstName();
        $user_lastname = $update->getMessage()->getFrom()->getLastName();

        $message = $update->getMessage()->getText();

        preg_match('/^\/([^\s@]+)@?(\S+)?\s?(.*)$/', $message, $matches);
        if (!empty($matches)) {
            return null;
        }

        $message_replay_type = 'thread_id';
        $reply = $update->getMessage()->getReplyToMessage();
        $parent_id = $this->zendeskUtils->getExternalID([$user_id, $chat_id]);
        if ($reply) {
            $parent_id = $this->zendeskUtils->getExternalID([$reply->getFrom()->get('id'), $reply->getChat()->get('id'), $reply->get('message_id')]);
        }

        return [
            'external_id' => $this->zendeskUtils->getExternalID([$user_id, $chat_id, $message_id]),
            'message' => $message,
            $message_replay_type => $parent_id,
            'created_at' => gmdate('Y-m-d\TH:i:s\Z', $message_date),
            'author' => [
                'external_id' => $this->zendeskUtils->getExternalID([$user_id, $user_username]),
                'name' => $user_firstname . ' ' . $user_lastname
            ]
        ];
    }
}