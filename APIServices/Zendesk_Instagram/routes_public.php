<?php

$router->prefix('instagram')->group(function () use ($router) {
    $router->get('manifest','ZendeskController@getManifest');
    $router->post('admin_ui','ZendeskController@adminUI');
    $router->post('admin_create_facebook_registration', 'ZendeskController@admin_create_facebook_registration');
    $router->post('admin_wait_facebook', 'ZendeskController@admin_wait_facebook');
    $router->get('admin_facebook_auth','ZendeskController@admin_facebook_auth');
    $router->post('admin_ui_2','ZendeskController@admin_ui_2');
    $router->post('admin_validate_page','ZendeskController@admin_validate_page');
    $router->post('admin_ui_submit','ZendeskController@admin_ui_submit');

    $router->post('pull','ZendeskController@pull');
    $router->post('channelback','ZendeskController@channelback');
    $router->get('healthcheck','ZendeskController@healthcheck');
    $router->post('event_callback', 'ZendeskController@event_callback');

});