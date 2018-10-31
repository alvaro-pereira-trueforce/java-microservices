<?php

namespace APIServices\Telegram\Commands;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk_Telegram\Models\TicketScaffold;
use APIServices\Zendesk_Telegram\Services\TicketService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class NewRequestCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "newrequest";

    /**
     * @var string Command Description
     */
    protected $description = "Create a new request";

    public function handle($arguments)
    {
        try {
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            /** @var TelegramService $telegramService */
            $telegramService = App::make(TelegramService::class);
            $channelSettings = $telegramService->getChannelSettings();
            if (!empty($channelSettings) && !empty($channelSettings['locale'])) {
                App::setLocale($channelSettings['locale']);
            }

            /** @var TicketService $ticketService */
            $ticketService = App::make(TicketService::class);
            /** @var TicketScaffold $ticketScaffold */
            $ticketScaffold = App::makeWith(TicketScaffold::class, [
                'ticketSettings' => $channelSettings
            ]);

            if (!empty($ticketService) && !empty($ticketScaffold)) {
                Log::debug('Deleting Ticket Identifier');
                $parent_id = $ticketScaffold->generateBasicParentID($this->update->getMessage());
                $status = $ticketService->deleteByParentIdentifier($parent_id);
                Log::debug($status);
                Log::debug('-------------------------');
            }
        } catch (\Exception $exception) {
            Log::error("Telegram Command Error:");
            Log::error($exception->getMessage() . 'Line: ' . $exception->getLine() . $exception->getFile());
        }

        $this->replyWithMessage([
            'text' => 'Ok, a new request was created.',
            'reply_to_message_id' => $this->getUpdate()->getMessage()->getMessageId()
        ]);
    }
}