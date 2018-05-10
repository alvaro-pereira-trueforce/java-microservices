<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Contact extends MessageType {

    function getTransformedMessage() {
        try {
            $contact = $this->message->getContact();
            $firstName = $contact->getFirstName();
            $lastName = $contact->getLastName();
            $phoneNumber = $contact->getPhoneNumber();

            $message = $this->getAuthorName() . ' sent a Contact';

            $basic_response = $this->zendeskUtils->getBasicResponse(
                $this->getExternalID(),
                $message,
                'thread_id',
                $this->parent_id,
                $this->message_date,
                $this->getAuthorExternalID(),
                $this->getAuthorName()
            );

            $response = $this->zendeskUtils->addHtmlMessageToBasicResponse($basic_response,
                view('telegram.contact_viewer', [
                    'message' => $message,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'phoneNumber' => $phoneNumber
                ])->render()
            );
            return $response;
        } catch (\Exception $exception) {
            return null;
        }
    }
}