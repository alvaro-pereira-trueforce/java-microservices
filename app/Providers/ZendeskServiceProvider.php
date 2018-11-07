<?php

namespace App\Providers;

use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use APIServices\Zendesk_Telegram\Models\EventsTypes\DestroyIntegrationEvent;
use APIServices\Zendesk_Telegram\Models\EventsTypes\UninstallIntegrationEvent;
use APIServices\Zendesk_Instagram\Models\EventTypes\DestroyIntegrationEvent as InstagramDestroyIntegrationEvent;
use APIServices\Zendesk_Instagram\Models\EventTypes\UninstallIntegrationEvent as InstagramUninstallIntegrationEvent;
use APIServices\Zendesk_Linkedin\Models\EventTypes\DestroyIntegrationEvent as LinkedInDestroyIntegrationEvent;
use APIServices\Zendesk_Linkedin\Models\EventTypes\UninstallIntegrationEvent as LinkedInUninstallIntegrationEvent;

use APIServices\Zendesk_Linkedin\Factories\SubEventsFactory as SubEventsFactory;
use APIServices\Zendesk_Linkedin\Models\EventTypes\CreatedPostEvent as CreatedPostEvent;

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
        $this->app->bind('instagram_destroy_integration', InstagramUninstallIntegrationEvent::class);

        //This configuration is for LinkedIn.
        $this->app->bind('linkedin_destroy_integration_instance', LinkedInDestroyIntegrationEvent::class);
        $this->app->bind('linkedin_destroy_integration', LinkedInUninstallIntegrationEvent::class);

        //This is a test for LinkedIn.
        $this->app->bind('linkedin_resources_created_from_external_ids',SubEventsFactory::class);
        $this->app->bind('linkedin_comment_on_new_ticket',CreatedPostEvent::class);
    }
}
