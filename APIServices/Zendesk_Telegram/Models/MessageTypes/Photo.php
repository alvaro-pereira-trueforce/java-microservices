<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Photo extends FilesMessageType {

    function getTransformedMessage() {
        $photoSize = $this->message->getPhoto();
        $photoURL = $this->telegramService->getPhotoURL($photoSize[3], $this->uuid);
        $message = $this->message->getCaption() ? $this->message->getCaption() : 'Photo from: ' . $this->user_firstname . ' ' . $this->user_lastname;
        $link = $this->getLocalURLFromExternalURL($photoURL);

        $basic_response = $this->zendeskUtils->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->getParentID(),
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->user_firstname . ' ' . $this->user_lastname);

        $response = $this->zendeskUtils->addHtmlMessageToBasicResponse($basic_response,
            view('telegram.photo_viewer', [
                'title' => 'Photo Sent from Telegram:',
                'photoURL' => env('APP_URL') . $link,
                'message' => $message
            ])->render()
        );
        $response = $this->zendeskUtils->addFilesURLToBasicResponse($response, [$link]);
        return $response;
    }
}