<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;


/**
 * Interface IMessageType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
interface IMessageType
{
    /**
     * @param $message
     * @param $access_token
     * @return mixed
     */
    function getTransformedMessage($message, $access_token);


}