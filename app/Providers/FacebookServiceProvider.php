<?php

namespace App\Providers;

use APIServices\Facebook\Models\Facebook;
use APIServices\Zendesk\Models\Formatters\Instagram\ImagePostFormatter;
use APIServices\Zendesk\Models\Formatters\Instagram\VideoPostFormatter;
use Facebook\Facebook as FB;
use Illuminate\Http\Request;
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

        //Instagram channel Message  Types
        $this->app->bind('INSTAGRAM.IMAGE', ImagePostFormatter::class);
        $this->app->bind('INSTAGRAM.VIDEO', VideoPostFormatter::class);

        $this->app->when(FB::class)
            ->needs('$config')
            ->give($facebookData);

        $this->app->when(Facebook::class)
            ->needs('$config')
            ->give($facebookData);

        $request = $this->app->make(Request::class);
        $metadata = json_decode($request->metadata, true);
        $state = json_decode($request->state, true);
        $state = !!$state ? $state : [];

        $this->app->when(Facebook::class)
            ->needs('$access_token')
            ->give(function () use ($metadata) {
                $access_token = '';
                if ($metadata) {
                    $access_token = $metadata['token'];
                }
                return $access_token;
            });

        $this->app->when(Facebook::class)
            ->needs('$instagram_id')
            ->give(function () use ($metadata) {
                $instagram_id = '';
                if ($metadata) {
                    $instagram_id = $metadata['instagram_id'];
                }
                return $instagram_id;
            });

        $this->app->when(Facebook::class)
            ->needs('$page_id')
            ->give(function () use ($metadata) {
                $page_id = '';
                if ($metadata) {
                    $page_id = $metadata['page_id'];
                }
                return $page_id;
            });
    }
}
