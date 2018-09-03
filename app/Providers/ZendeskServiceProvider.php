<?php

namespace App\Providers;

use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use APIServices\Zendesk_Telegram\Models\EventsTypes\DestroyIntegrationEvent;
use APIServices\Zendesk_Telegram\Models\EventsTypes\UninstallIntegrationEvent;
use APIServices\Zendesk_Instagram\Models\EventTypes\DestroyIntegrationEvent as InstagramDestroyIntegrationEvent;
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

        //This is the configuration for Telegram upgrade the naming later.
        $this->app->bind('destroy_integration_instance', DestroyIntegrationEvent::class);
        $this->app->bind('destroy_integration', UninstallIntegrationEvent::class);

        //This configuration is for Instagram.
        $this->app->bind('instagram_destroy_integration_instance', InstagramDestroyIntegrationEvent::class);
    }
}
