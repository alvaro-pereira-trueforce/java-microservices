<?php
namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Photo extends MessageType {

    function getTransformedMessage($update) {
        $photoSize = $update->getMessage()->getPhoto();

        $photoURL = $this->telegramService->getPhotoURL($photoSize[3], $this->uuid);

        $message_id = $update->getMessage()->getMessageId();
        $user_id = $update->getMessage()->getFrom()->getId();
        $chat_id = $update->getMessage()->getChat()->getId();
        $message_date = $update->getMessage()->getDate();
        $user_username = $update->getMessage()->getFrom()->getUsername();
        $user_firstname = $update->getMessage()->getFrom()->getFirstName();
        $user_lastname = $update->getMessage()->getFrom()->getLastName();

        $message = $update->getMessage()->getCaption() ? $update->getMessage()->getCaption() : 'Photo from: '.$user_firstname . ' ' . $user_lastname;

        $message_replay_type = 'thread_id';
        $reply = $update->getMessage()->getReplyToMessage();
        $parent_id = $this->zendeskUtils->getExternalID([$user_id, $chat_id]);
        if ($reply) {
            $parent_id = $this->zendeskUtils->getExternalID([$reply->getFrom()->get('id'), $reply->getChat()->get('id'), $reply->get('message_id')]);
        }

        $link = $this->getLocalURLFromExternalURL($photoURL);

        return [
            'external_id' => $this->zendeskUtils->getExternalID([$user_id, $chat_id, $message_id]),
            'message' => $message,
            'html_message' => view('telegram.photo_viewer',[
                'title' => 'Photo Sent from Telegram',
                'photoURL' => env('APP_URL').$link,
                'message' => $message
            ])->render(),
            $message_replay_type => $parent_id,
            'created_at' => gmdate('Y-m-d\TH:i:s\Z', $message_date),
            'author' => [
                'external_id' => $this->zendeskUtils->getExternalID([$user_id, $user_username]),
                'name' => $user_firstname . ' ' . $user_lastname
            ],
            'file_urls' => [$link]
        ];
    }
}