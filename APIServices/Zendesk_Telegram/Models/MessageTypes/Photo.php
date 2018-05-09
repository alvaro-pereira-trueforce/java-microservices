<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Photo extends FilesMessageType {

    function getTransformedMessage() {

        $photoSizes = $this->message->getPhoto();

        $maxSize = 0;
        $current_photo = null;
        foreach ($photoSizes as $photo) {
            if ((int)$photo['file_size'] > $maxSize) {
                $maxSize = $photo['file_size'];
                $current_photo = $photo;
            }
        }

        $photoURL = $this->telegramService->getPhotoURL($current_photo);
        $message = $this->getValidCaptionMessage('Photo');
        $link = $this->getLocalURLFromExternalURL($photoURL);

        $basic_response = $this->zendeskUtils->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->parent_id,
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->getAuthorName());

        $response = $this->zendeskUtils->addHtmlMessageToBasicResponse($basic_response,
            view('telegram.photo_viewer', [
                'title' => '',
                'photoURL' => env('APP_URL') . $link,
                'message' => $message
            ])->render()
        );
        $response = $this->zendeskUtils->addFilesURLToBasicResponse($response, [$link]);
        return $response;
    }
}