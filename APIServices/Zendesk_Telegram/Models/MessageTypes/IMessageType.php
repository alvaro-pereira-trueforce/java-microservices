<?php
namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


interface IMessageType {
    /**
     * @return array
     */
    function getTransformedMessage();
}