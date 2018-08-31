<?php

namespace App\Providers;


use APIServices\Zendesk_Instagram\Models\WebHooks;
use Illuminate\Support\ServiceProvider;

class InstagramServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {

        //Instagram Webhook Payload Types
        $this->app->bind('instagram_comments', WebHooks\CommentPayload::class);
    }
}