<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;

class Video extends FilesMessageType {

    function getTransformedMessage() {
        $video = $this->message->getVideo();

        try
        {
            if((int) $video->getFileSize() > 20000000)
            {
                $this->telegramService->sendTelegramMessage(
                    $this->chat_id,
                    'Our support size limit was reached (20MB), please try to send the Video in short parts'
                );
                return null;
            }

            $videoURL = $this->telegramService->getDocumentURL($video->getFileId());

            $basic_document_response = $this->getBasicDocumentResponse('Video', $videoURL,
                $video->getFileId());

            $thumbURL = $this->telegramService->getDocumentURL($video->getThumb()->getFileId());

            $thumbURL = $this->getLocalURLFromExternalURL($thumbURL, $video->getThumb()->getFileId());

            return $this->zendeskUtils->addHtmlMessageToBasicResponse($basic_document_response,
                view('telegram.photo_viewer', [
                    'title' => '',
                    'photoURL' => env('APP_URL') . $thumbURL,
                    'message' => $basic_document_response['message']
                ])->render()
            );
        }catch (\Exception $exception)
        {
            return null;
        }
    }
}