<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;

/**
 * This is an interface to handle the process of transform the data whether they are
 * Comments, Images or Videos.
 * Interface IMessageTransform
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
interface IMessageTransformer
{
    /**
     * @param $message
     * @return array
     */
    function getTransformedMessage($message);


}