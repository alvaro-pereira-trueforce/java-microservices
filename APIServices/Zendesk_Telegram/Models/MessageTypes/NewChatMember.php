<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class NewChatMember extends MessageType {

    function getTransformedMessage() {
        $newChatParticipant = $this->update->getMessage()->getNewChatParticipant();
        if (!$newChatParticipant && $this->chat_type != 'group') {
            return null;
        }

        $message = $newChatParticipant->getFirstName() . ' was added to the group: ' .
            $this->message->getChat()->getTitle();

        $this->telegramService->triggerCommand('start', '', $this->update);

        return $this->zendeskUtils->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->parent_id,
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->getAuthorName()
        );
    }
}