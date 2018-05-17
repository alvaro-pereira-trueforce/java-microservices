<?php

namespace App\Providers;

use APIServices\Facebook\Models\Facebook;
use Facebook\Facebook as FB;
use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //Facebook Service Configuration
        $facebookData = [
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => env('FACEBOOK_DEFAULT_GRAPH_VERSION')
        ];

        $this->app->when(FB::class)
            ->needs('$config')
            ->give($facebookData);

        $this->app->when(Facebook::class)
            ->needs('$config')
            ->give($facebookData);
    }
}
