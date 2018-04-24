<?php
$router->prefix('instagram')->group(function () use ($router) {
    $router->get('manifest','ZendeskController@getManifest');
    $router->post('admin_ui','ZendeskController@adminUI');
    $router->post('admin_ui_2','ZendeskController@admin_ui_2');
    $router->post('admin_ui_2/{uuid}','ZendeskController@handleSubmitForAdminUI');
    $router->post('admin_ui_2/delete/{uuid}','ZendeskController@handleDeleteForAdminUI');
    $router->post('channelback','ZendeskController@channelback');


    $router->post('pull','ZendeskController@pull');
    $router->post('event_callback', 'ZendeskController@event_callback');

});