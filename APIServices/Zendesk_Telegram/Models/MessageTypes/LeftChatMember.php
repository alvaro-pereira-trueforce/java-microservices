<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class LeftChatMember extends MessageType {

    function getTransformedMessage() {
        $leftChatParticipant = $this->update->getMessage()->getLeftChatParticipant();
        if (!$leftChatParticipant && $this->chat_type != 'group') {
            return null;
        }

        $message = $leftChatParticipant->getFirstName() . ' was deleted from the group: ' .
            $this->message->getChat()->getTitle();

        return $this->zendeskUtils->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->parent_id,
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->getAuthorName());
    }
}