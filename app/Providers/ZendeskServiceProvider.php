<?php

namespace App\Providers;

use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;

use APIServices\Zendesk_Telegram\Models\EventsTypes\DestroyIntegrationEvent;
use APIServices\Zendesk_Telegram\Models\EventsTypes\UninstallIntegrationEvent;
use APIServices\Zendesk_Instagram\Models\EventTypes\DestroyIntegrationEvent as InstagramDestroyIntegrationEvent;
use APIServices\Zendesk_Instagram\Models\EventTypes\UninstallIntegrationEvent as InstagramUninstallIntegrationEvent;
use APIServices\Zendesk_Linkedin\Models\EventTypes\DestroyIntegrationEvent as LinkedInDestroyIntegrationEvent;
use APIServices\Zendesk_Linkedin\Models\EventTypes\UninstallIntegrationEvent as LinkedInUninstallIntegrationEvent;
//these are the routes for linkedin create ticket
use APIServices\Zendesk_Linkedin\Factories\ResourceEventTypeFactory as ResourceEventTypeFactory;
use APIServices\Zendesk_Linkedin\Models\EventTypes\CreatedPostEvent as CreatedPostEvent;
//these are the routes for linkedin commands events
use APIServices\Zendesk_Linkedin\Models\CommandTypes\ProfileList as ProfileList;
use APIServices\Zendesk_Linkedin\Models\CommandTypes\CompanyInformation as CompanyInformation;
use APIServices\Zendesk_Linkedin\Models\CommandTypes\Statistics as Statistics;
use APIServices\Zendesk_Linkedin\Models\CommandTypes\StatisticsCounts as StatisticsCounts;
use APIServices\Zendesk_Linkedin\Models\CommandTypes\StatisticsFunctions as StatisticsFunctions;
use APIServices\Zendesk_Linkedin\Models\CommandTypes\StatisticsSeniorities as StatisticsSeniorities;
use APIServices\Zendesk_Linkedin\Models\CommandTypes\StatisticsCountries as StatisticsCountries;


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
        $this->app->bind('linkedin_resources_created_from_external_ids', ResourceEventTypeFactory::class);
        $this->app->bind('linkedin_comment_on_new_ticket', CreatedPostEvent::class);

        //This is for CommandsLinkedin
        $this->app->bind('linkedin_s@getlist', ProfileList::class);
        $this->app->bind('linkedin_s@getcompany', CompanyInformation::class);
        $this->app->bind('linkedin_s@getstatistics', Statistics::class);
        $this->app->bind('linkedin_s@getstatistics_count', StatisticsCounts::class);
        $this->app->bind('linkedin_s@getstatistics_functions', StatisticsFunctions::class);
        $this->app->bind('linkedin_s@getstatistics_seniorities', StatisticsSeniorities::class);
        $this->app->bind('linkedin_s@getstatistics_countries',StatisticsCountries::class);


    }
}
