<?php

namespace App\Providers;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Document;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Photo;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        /**
         * Get common request variables from zendesk
         */

        $request = $this->app->make(Request::class);
        $metadata = json_decode($request->metadata, true);
        $state = json_decode($request->state, true);
        $state = !!$state ? $state : [];

        //Telegram channel Message  Types
        $this->app->bind('telegram.text', Text::class);
        $this->app->bind('telegram.photo', Photo::class);
        $this->app->bind('telegram.document', Document::class);

        $this->app->when(TelegramService::class)
            ->needs('$uuid')
            ->give(function () use ($metadata) {
                $uuid = '';
                if ($metadata) {
                    $uuid = $metadata['token'];
                }
                return $uuid;
            });
        
        $message_types = [Text::class, Photo::class, Document::class];

        foreach ($message_types as $type) {
            $this->app->when($type)
                ->needs('$state')
                ->give(function () use ($state) {
                    return $state;
                });
        }
    }
}
