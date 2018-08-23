<?php

$router->prefix('instagram')->group(function () use ($router) {
    $router->get('manifest', 'ZendeskController@getManifest');
    $router->get('admin_ui', 'ZendeskController@admin_UI_login');
    $router->post('admin_ui', 'ZendeskController@admin_UI');
    $router->post('admin_UI_waiting', 'ZendeskController@admin_UI_waiting');
    $router->get('admin_ui_save/{account_id}', 'ZendeskController@admin_ui_save');
    $router->post('pull', 'ZendeskController@pull');
    $router->post('channel_back', 'ZendeskController@channel_back');
    $router->get('click_through', 'ZendeskController@click_through');
    $router->get('health_check', 'ZendeskController@health_check');
    $router->post('event_callback', 'ZendeskController@event_callback');
});