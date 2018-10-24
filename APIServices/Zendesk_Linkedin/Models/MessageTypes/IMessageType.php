<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;

/**
 * This is an interface to handle the process of transform the data whether they are
 * Comments, Images or Videos.
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