<?php

$router->prefix('linkedin')->group(function () use ($router) {
    $router->get('manifest','ZendeskController@getManifest');
    $router->post('admin_ui','ZendeskController@admin_UI');
    $router->post('pull','ZendeskController@pull');
    $router->post('channel_back','ZendeskController@channel_back');
    $router->get('click_through','ZendeskController@click_through');
    $router->get('health_check','ZendeskController@health_check');
    $router->post('event_callback', 'ZendeskController@event_callback');
});
