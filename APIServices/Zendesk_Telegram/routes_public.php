<?php

$router->prefix('telegram')->group(function () use ($router) {
    $router->get('manifest','ZendeskController@getManifest');
    $router->post('admin_ui','ZendeskController@adminUI');
    $router->post('admin_ui_2','ZendeskController@admin_ui_2');
    $router->post('pull','ZendeskController@pull');
    $router->post('channelback','ZendeskController@channelback');
    $router->get('clickthrough','ZendeskController@clickthrough');
    $router->get('healthcheck','ZendeskController@healthcheck');
    $router->post('event_callback', 'ZendeskController@event_callback');
});
