<?php
$router->prefix('instagram')->group(function () use ($router) {
    $router->get('manifest','ZendeskController@getManifest');
});