<?php

$router->prefix('telegram')->group(function () use ($router) {
    $router->get('manifest','ZendeskController@getManifest');
    $router->post('admin_ui','ZendeskController@admin_UI');
    $router->post('admin_ui_validate_data','ZendeskController@admin_ui_validate_data');
    $router->get('admin_ui_save/{account_id}','ZendeskController@admin_ui_save');
    $router->post('pull','ZendeskController@pull');
    $router->post('channelback','ZendeskController@channel_back');
    $router->get('clickthrough','ZendeskController@click_through');
    $router->get('healthcheck','ZendeskController@health_check');
    $router->post('event_callback', 'ZendeskController@event_callback');
});
