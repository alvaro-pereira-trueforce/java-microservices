<?php

namespace App\Providers;

use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use APIServices\Zendesk_Telegram\Models\EventsTypes\DestroyIntegrationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class ZendeskServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $request = $this->app->make(Request::class);
        $state = json_decode($request->state, true);
        $state = !!$state ? $state : [];

        $this->app->when(ZendeskChannelService::class)
            ->needs('$state')
            ->give($state);

        $this->app->bind('destroy_integration_instance', DestroyIntegrationEvent::class);
    }
}
