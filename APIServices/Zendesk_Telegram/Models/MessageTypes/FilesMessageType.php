<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Storage;

abstract class FilesMessageType extends MessageType {

    public function getLocalURLFromExternalURL($external_url) {
        $contents = file_get_contents($external_url);
        $name = substr($external_url, strrpos($external_url, '/') + 1);
        Storage::put($name, $contents);
        return Storage::url($name);
    }

    protected function getValidCaptionMessage($file_type)
    {
        return $this->message->getCaption() ? $this->message->getCaption() :
            $this->getAuthorName() . ' sent a '.$file_type;
    }

    protected function getBasicDocumentResponse($file_type, $external_url)
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
        $link = $this->getLocalURLFromExternalURL($external_url);

        return $this->zendeskUtils->addFilesURLToBasicResponse($basic_response, [$link]);
    }
}