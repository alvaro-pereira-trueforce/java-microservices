<?php

Route::post('/{token}/webhook', 'WebhookController@webhookHandler');