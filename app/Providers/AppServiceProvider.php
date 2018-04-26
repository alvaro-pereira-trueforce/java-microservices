<?php

namespace App\Providers;

use APIServices\Zendesk_Telegram\Models\MessageTypes\Document;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Photo;
use APIServices\Zendesk_Telegram\Models\MessageTypes\Text;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Telegram channel Message  Types
        $this->app->bind('telegram.text',Text::class);
        $this->app->bind('telegram.photo',Photo::class);
        $this->app->bind('telegram.document',Document::class);
    }
}
