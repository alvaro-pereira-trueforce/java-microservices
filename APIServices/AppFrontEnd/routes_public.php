<?php

$router->get('/', function () {
    return file_get_contents(public_path().'/index.html');
});
