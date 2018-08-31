<?php

namespace APIServices\Zendesk_Instagram\Models\WebHooks;


use APIServices\Zendesk\Models\IMessageType;

abstract class MessageType implements IMessageType
{
    protected $field_id;

    /**
     * MessageType constructor.
     * @param $field_id
     */
    public function __construct($field_id)
    {
        $this->field_id = $field_id;
    }
}