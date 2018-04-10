<?php
//$router->post('/users', 'UserController@create');
/***
 * @var \APIServices\Telegram\Services\ChannelService::class
 */
$telegram_repository = \Illuminate\Support\Facades\App::make(\APIServices\Telegram\Services\ChannelService::class);

$telegramBots = $telegram_repository->getAll();

foreach ($telegramBots as $bot)
{
    Route::post('/'.$bot->token.'/webhook', 'WebhookController@webhookHandler');
}