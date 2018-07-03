<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;

abstract class FilesMessageType extends MessageType {

    /**
     * Get a local URL to be handler with the download Endpoint
     * @param $external_url
     * @param $file_id
     * @return string
     * @throws \Exception
     */
    public function getLocalURLFromExternalURL($external_url, $file_id) {
        try{
            $name = substr($external_url, strrpos($external_url, '/') + 1);
            return '/files/'.$name.'?uuid='.$this->telegramService->getCurrentUUID().'&id='.$file_id;
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    protected function getValidCaptionMessage($file_type)
    {
        return $this->message->getCaption() ? $this->message->getCaption() :
            $this->getAuthorName() . ' sent a '.$file_type;
    }

    /**
     * @param $file_type
     * @param $external_url
     * @param $file_id
     * @return array
     * @throws \Exception
     */
    protected function getBasicDocumentResponse($file_type, $external_url, $file_id)
    {
        try
        {
            $message = $this->getValidCaptionMessage($file_type);
            $basic_response = $this->getBasicResponse(
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
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }
}