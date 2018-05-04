<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Document extends FilesMessageType {

    function getTransformedMessage() {
        $document = $this->message->getDocument();
        $documentURL = $this->telegramService->getDocumentURL($document, $this->uuid);

        $message = $this->message->getCaption() ? $this->message->getCaption() : 'Document from: ' .
            $this->user_firstname . ' ' . $this->user_lastname;

        $basic_response = $this->zendeskUtils->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->getParentID(),
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->user_firstname . ' ' . $this->user_lastname);

        $link = $this->getLocalURLFromExternalURL($documentURL);

        return $this->zendeskUtils->addFilesURLToBasicResponse($basic_response, [$link]);
    }
}