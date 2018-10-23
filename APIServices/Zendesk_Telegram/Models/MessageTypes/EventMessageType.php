<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Models\TicketScaffold;
use APIServices\Zendesk_Telegram\Services\TicketService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;

abstract class EventMessageType extends MessageType
{
    /**
     * EventMessageType constructor.
     * @param TicketService $ticketService
     * @param Utility $zendeskUtils
     * @param Update $update
     * @param array $state
     * @param TelegramService $telegramService
     * @throws \Exception
     */
    public function __construct(TicketService $ticketService, Utility $zendeskUtils, Update $update, array $state, TelegramService $telegramService)
    {
        try {
            $ticketSettings = $telegramService->getChannelSettings();
            $this->ticketSettings = array_filter($ticketSettings);
            $this->ticketScaffold = App::makeWith(TicketScaffold::class, [
                'zendeskUtils' => $zendeskUtils,
                'ticketService' => $ticketService,
                'ticketSettings' => $this->ticketSettings
            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
        $this->ticketService = $ticketService;
        $this->zendeskUtils = $zendeskUtils;
        $this->update = $update;
        $this->state = $state;
        $this->telegramService = $telegramService;
    }
}