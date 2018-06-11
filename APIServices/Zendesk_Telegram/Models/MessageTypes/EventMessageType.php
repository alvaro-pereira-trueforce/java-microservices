<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Models\TicketScaffold;
use APIServices\Zendesk_Telegram\Services\TicketService;
use Illuminate\Support\Facades\App;
use Telegram\Bot\Objects\Update;

abstract class EventMessageType extends MessageType {

    public function __construct(TicketService $ticketService, Utility $zendeskUtils, Update $update, array $state, TelegramService $telegramService) {
        $this->ticketScaffold = App::makeWith(TicketScaffold::class, [
            'zendeskUtils' => $zendeskUtils,
            'ticketService' => $ticketService
        ]);
        $this->ticketService = $ticketService;
        $this->zendeskUtils = $zendeskUtils;
        $this->update = $update;
        $this->state = $state;
        $this->telegramService = $telegramService;
    }
}