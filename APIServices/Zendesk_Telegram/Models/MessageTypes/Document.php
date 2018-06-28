<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Document extends FilesMessageType {

    /**
     * @return array
     * @throws \Exception
     */
    function getTransformedMessage() {
        try
        {
            $document = $this->message->getDocument();
            $documentURL = $this->telegramService->getDocumentURL($document->getFileId());
            return $this->getBasicDocumentResponse('Document', $documentURL, $document->getFileId());
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }
}