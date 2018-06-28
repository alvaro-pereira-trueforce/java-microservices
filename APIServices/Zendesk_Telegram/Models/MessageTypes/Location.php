<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Location extends FilesMessageType {

    function getTransformedMessage() {
        $location = $this->message->getLocation();
        $latitude = $location->getLatitude();
        $longitude = $location->getLongitude();

        $message = $this->getAuthorName() . ' sent a Location';

        $basic_response = $this->getBasicResponse(
            $this->getExternalID(),
            $message,
            'thread_id',
            $this->parent_id,
            $this->message_date,
            $this->getAuthorExternalID(),
            $this->getAuthorName()
        );

        $response = $this->zendeskUtils->addHtmlMessageToBasicResponse($basic_response,
            view('telegram.maps_viewer', [
                'message' => $message,
                'latitude' => $latitude,
                'longitude' => $longitude
            ])->render()
        );

        return $response;
    }
}