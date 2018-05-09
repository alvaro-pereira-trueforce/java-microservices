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
                    'Our support size limit was reached, please try to send the Video in short parts'
                );
                return null;
            }

            $videoURL = $this->telegramService->getDocumentURL($video);

            $basic_document_response = $this->getBasicDocumentResponse('Video', $videoURL);

            $thumbURL = $this->telegramService->getDocumentURL($video->getThumb());

            $thumbURL = $this->getLocalURLFromExternalURL($thumbURL);

            return $this->zendeskUtils->addHtmlMessageToBasicResponse($basic_document_response,
                view('telegram.photo_viewer', [
                    'title' => '',
                    'photoURL' => env('APP_URL') . $thumbURL,
                    'message' => ''
                ])->render()
            );
        }catch (\Exception $exception)
        {
            return null;
        }
    }
}