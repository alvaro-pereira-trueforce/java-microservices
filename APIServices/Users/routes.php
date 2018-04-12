<?php

$router->get('/users', 'UserController@getAll');
$router->get('/users/{uuid}', 'UserController@getById');
$router->post('/users', 'UserController@create');
$router->put('/users/{uuid}', 'UserController@update');
$router->delete('/users/{uuid}', 'UserController@delete');