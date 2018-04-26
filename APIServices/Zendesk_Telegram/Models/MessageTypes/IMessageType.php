<?php
namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


use Telegram\Bot\Objects\Update;

interface IMessageType {
    /**
     * @param Update $message
     * @return array
     */
    function getTransformedMessage($message);
}