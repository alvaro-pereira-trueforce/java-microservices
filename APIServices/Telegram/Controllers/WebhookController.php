<?php

namespace APIServices\Telegram\Controllers;

use APIServices\Zendesk_Telegram\Jobs\ProcessTelegramMessage;
use App\Http\Controllers\Controller;
use Telegram\Bot\Api;

class WebhookController extends Controller
{
    /**
     * @var Api
     */
    protected $telegram;

    /**
     * BotController constructor.
     *
     * @param Api $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function webhookHandler($token)
    {
        $this->telegram->setAccessToken($token);
        $this->telegram->setAsyncRequest(false);
        $update = $this->telegram->commandsHandler(true);
        ProcessTelegramMessage::dispatch($update, $token);

        return response()->json('ok', 200);
    }

    /* This is an basic example
     * public function webhookHandler($token)
    {
        // If you're not using commands system, then you can enable this.
        // $update = $this->telegram->getWebhookUpdate();

        //This will set the access token to replay the correct registered bot
        $this->telegram->setAccessToken($token);
        // This fetchs webhook update + processes the update through the commands system.
        $update = $this->telegram->commandsHandler(true);
        Log::info($update);

        // Commands handler method returns an Update object.
        // So you can further process $update object
        // to however you want.

        // Below is an example
        $message = $update->getMessage();

        // Triggers when your bot receives text messages like:
        // - Can you inspire me?
        // - Do you have an inspiring quote?
        // - Tell me an inspirational quote
        // - inspire me
        // - Hey bot, tell me an inspiring quote please?
        //if(str_contains($message->text, ['inspire', 'inspirational', 'inspiring'])) {
        //  $this->telegram->sendMessage()
        //      ->chatId($message->chat->id)
        //      ->text(Inspiring::quote())
        //      ->getResult();
        //}
        Log::info($message);
        return response()->json('ok',200);
    }*/
}