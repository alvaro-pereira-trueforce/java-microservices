<?php

return [
    'namespaces' => [
        'APIServices' => base_path() . DIRECTORY_SEPARATOR . 'APIServices',
        'App' => base_path() . DIRECTORY_SEPARATOR . 'app'
    ],


    'protection_middleware' => [
        'api'
    ],

    'resource_namespace' => 'resources',

    'language_folder_name' => 'lang',

    'view_folder_name' => 'views'
];
