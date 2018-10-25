<?php

namespace APIServices\Zendesk_Instagram\Models\MessageTypes;


use APIServices\Zendesk\Models\IMessageType;

abstract class MessageType implements IMessageType
{
    protected $payload;

    /**
     * MessageTransform constructor.
     * @param $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}