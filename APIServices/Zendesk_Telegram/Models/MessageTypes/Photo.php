<?php
namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Storage;

class Photo implements IMessageType {

    protected $telegramService;
    protected $uuid;
    protected $zendeskUtils;

    public function __construct(TelegramService $telegramService, $uuid, Utility $zendeskUtils) {
        $this->telegramService = $telegramService;
        $this->uuid = $uuid;
        $this->zendeskUtils = $zendeskUtils;
    }

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

        $contents = file_get_contents($photoURL);
        $name = $photoSize[3]['file_id'];
        Storage::disk('public')->put($name, $contents);
        $url = Storage::url($name);

        return [
            'external_id' => $this->zendeskUtils->getExternalID([$user_id, $chat_id, $message_id]),
            'message' => $message,
            'html_message' => sprintf('<p><img src=%s></p>', env('APP_URL').$url),
            $message_replay_type => $parent_id,
            'created_at' => gmdate('Y-m-d\TH:i:s\Z', $message_date),
            'author' => [
                'external_id' => $this->zendeskUtils->getExternalID([$user_id, $user_username]),
                'name' => $user_firstname . ' ' . $user_lastname
            ]
        ];
    }
}