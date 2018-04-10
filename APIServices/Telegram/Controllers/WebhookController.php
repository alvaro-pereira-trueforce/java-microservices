<?php
namespace APIServices\Telegram\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
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


    public function webhookHandler()
    {
        // If you're not using commands system, then you can enable this.
//        $update = $this->telegram->getWebhookUpdate();

        // This fetchs webhook update + processes the update through the commands system.
        $update = $this->telegram->commandsHandler(true);


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
        /*if(str_contains($message->text, ['inspire', 'inspirational', 'inspiring'])) {
            $this->telegram->sendMessage()
                ->chatId($message->chat->id)
                ->text(Inspiring::quote())
                ->getResult();
        }*/
        Log::info($message);

        return 'Ok';
    }
}