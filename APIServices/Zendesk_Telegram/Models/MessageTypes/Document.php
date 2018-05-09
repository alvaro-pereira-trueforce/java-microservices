<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Document extends FilesMessageType {

    function getTransformedMessage() {
        $document = $this->message->getDocument();
        $documentURL = $this->telegramService->getDocumentURL($document);
        return $this->getBasicDocumentResponse('Document', $documentURL);
    }
}