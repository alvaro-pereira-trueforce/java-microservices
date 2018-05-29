<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class UnknownType extends MessageType {

    function getTransformedMessage() {
        return null;
    }
}