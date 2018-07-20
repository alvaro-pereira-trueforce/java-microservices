<?php

namespace APIServices\Telegram\Commands;

use APIServices\Telegram\Services\TelegramService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class CancelCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "cancel";

    /**
     * @var string Command Description
     */
    protected $description = "Cancel any started command.";

    public function handle($arguments)
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        try
        {
            $this->replyWithMessage([
                'text' => 'canceled...'
            ]);
            $this->replyWithMessage([
                'text' => 'Ok '. $user_id = $this->update->getMessage()->getFrom()->getFirstName().
                        ', cancelled.'
            ]);
            /** @var TelegramService $service */
            $service = App::make(TelegramService::class);
            $service->cancelStartedCommand($this->update);
        }catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
        }
    }
}