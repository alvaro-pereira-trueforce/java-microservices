<?php
$router->prefix('instagram')->group(function () use ($router) {
    //$router->get('manifest','ZendeskController@getManifest');
    //$router->post('pull','ZendeskController@pull');

    $router->get('testClass','ZendeskController@getTestClass');
});