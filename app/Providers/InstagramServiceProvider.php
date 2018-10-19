<?php

namespace App\Providers;


use APIServices\Zendesk_Instagram\Models\MessageTypes;
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
        $this->app->bind('instagram_comments', MessageTypes\CommentPayload::class);

        //Instagram channel Media Types
        $this->app->bind('instagram_IMAGE', MessageTypes\ImageMediaType::class);
        $this->app->bind('instagram_VIDEO', MessageTypes\VideoMediaType::class);
        $this->app->bind('instagram_CAROUSEL_ALBUM', MessageTypes\CarouselAlbumType::class);
    }
}