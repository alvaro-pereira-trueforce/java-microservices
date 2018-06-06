<?php

namespace App\Providers;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Audio;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Contact;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Document;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Edited;
use APIServices\Zendesk_Telegram\Models\MessageTypes\LeftChatMember;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Location;
use APIServices\Zendesk_Telegram\Models\MessageTypes\NewChatMember;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Photo;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Sticker;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Text;
use APIServices\Zendesk_Telegram\Models\MessageTypes\UnknownType;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Video;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Voice;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        $request = $this->app->make(Request::class);
        $metadata = json_decode($request->metadata, true);
        $state = json_decode($request->state, true);
        $state = !!$state ? $state : [];

        //Telegram channel Message  Types
        $this->app->bind('telegram.text', Text::class);
        $this->app->bind('telegram.photo', Photo::class);
        $this->app->bind('telegram.document', Document::class);
        $this->app->bind('telegram.left_chat_member', LeftChatMember::class);
        $this->app->bind('telegram.left_chat_participant', LeftChatMember::class);
        $this->app->bind('telegram.new_chat_member', NewChatMember::class);
        $this->app->bind('telegram.new_chat_participant', NewChatMember::class);
        $this->app->bind('telegram.video', Video::class);
        $this->app->bind('telegram.audio', Audio::class);
        $this->app->bind('telegram.voice', Voice::class);
        $this->app->bind('telegram.contact', Contact::class);
        $this->app->bind('telegram.location', Location::class);
        $this->app->bind('telegram.edited', Edited::class);
        $this->app->bind('telegram.sticker', Sticker::class);
        $this->app->bind('telegram.', UnknownType::class);

        $this->app->when(TelegramService::class)
            ->needs('$uuid')
            ->give(function () use ($metadata) {
                $uuid = '';
                if ($metadata && array_key_exists('token', $metadata)) {
                    $uuid = $metadata['token'];
                }
                return $uuid;
            });

        $message_types = [
            Text::class, Photo::class, Document::class, UnknownType::class,
            LeftChatMember::class, NewChatMember::class, Video::class,
            Voice::class, Audio::class, Contact::class, Location::class, Edited::class,
            Sticker::class
        ];

        foreach ($message_types as $type) {
            $this->app->when($type)
                ->needs('$state')
                ->give(function () use ($state) {
                    return $state;
                });
        }
    }
}
