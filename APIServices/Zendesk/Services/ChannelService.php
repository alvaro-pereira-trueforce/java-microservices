<?php

namespace APIServices\Zendesk\Services;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

abstract class ChannelService implements IChannelService
{
    /**
     * Configure Dependency Container to use the Zendesk Access Token Domain and Instance Push ID
     * @param $zendesk_access_token
     * @param $subdomain
     * @param $instance_push_id
     */
    public function configureZendeskAPI($zendesk_access_token, $subdomain, $instance_push_id)
    {
        try {
            App::when(ZendeskClient::class)
                ->needs('$access_token')
                ->give($zendesk_access_token);
            App::when(ZendeskAPI::class)
                ->needs('$subDomain')
                ->give($subdomain);
            App::when(ZendeskAPI::class)
                ->needs('$instance_push_id')
                ->give($instance_push_id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}