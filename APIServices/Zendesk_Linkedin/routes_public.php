<?php

$router->prefix('linkedin')->group(function () use ($router) {
    $router->get('manifest','ZendeskController@getManifest');
    $router->post('admin_ui','ZendeskController@admin_UI');
    $router->post('pull','ZendeskController@pull');
    $router->post('channelback','ZendeskController@channel_back');
    $router->get('clickthrough','ZendeskController@click_through');
    $router->get('healthcheck','ZendeskController@health_check');
    $router->post('event_callback', 'ZendeskController@event_callback');
});
