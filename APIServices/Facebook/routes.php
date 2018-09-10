<?php

Route::get('/facebook/webhook', 'WebhookController@webhookSubscribe');
Route::post('/facebook/webhook', 'WebhookController@webhookHandler');

//Just for tests .- We can make a unit this with this steps
//Route::post('/test', 'WebhookController@test');