<?php
$router->get('/files/{filename}', 'StorageController@getFileToDownload');
$router->post('/files/{filename}', 'StorageController@getFileToDownload');
