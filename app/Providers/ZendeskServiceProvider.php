<?php

namespace App\Providers;

use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class ZendeskServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        $request = $this->app->make(Request::class);
        $state = json_decode($request->state, true);
        $state = !!$state ? $state : [];

        $this->app->when(ZendeskChannelService::class)
            ->needs('$state')
            ->give($state);
    }
}
