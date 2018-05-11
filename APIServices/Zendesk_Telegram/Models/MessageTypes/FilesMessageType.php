<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;

abstract class FilesMessageType extends MessageType {

    public function getLocalURLFromExternalURL($external_url, $file_id) {
        $name = substr($external_url, strrpos($external_url, '/') + 1);
        return '/files/'.$name.'?uuid='.$this->telegramService->getCurrentUUID().'&id='.$file_id;
    }

    protected function getValidCaptionMessage($file_type)
    {
        return $this->message->getCaption() ? $this->message->getCaption() :
            $this->getAuthorName() . ' sent a '.$file_type;
    }

    protected function getBasicDocumentResponse($file_type, $external_url, $file_id)
    {
        $message = $this->getValidCaptionMessage($file_type);
        $basic_response = $this->zendeskUtils->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->parent_id,
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->getAuthorName()
        );
        $link = $this->getLocalURLFromExternalURL($external_url, $file_id);

        return $this->zendeskUtils->addFilesURLToBasicResponse($basic_response, [$link]);
    }
}