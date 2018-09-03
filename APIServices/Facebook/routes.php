<?php

Route::get('/facebook/webhook', 'WebhookController@webhookSubscribe');
Route::post('/facebook/webhook', 'WebhookController@webhookHandler');