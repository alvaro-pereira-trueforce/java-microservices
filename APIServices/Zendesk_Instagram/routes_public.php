<?php
$router->prefix('instagram')->group(function () use ($router) {
    //$router->get('manifest','ZendeskController@getManifest');
    $router->post('pull','ZendeskController@pull');
    $router->post('event_callback', 'ZendeskController@event_callback');
   // $router->get('testClass','ZendeskController@getTestClass');
});