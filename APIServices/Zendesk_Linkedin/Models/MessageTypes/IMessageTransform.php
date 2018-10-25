<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;

/**
 * This is an interface to handle the process of transform the data whether they are
 * Comments, Images or Videos.
 * Interface IMessageTransform
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
interface IMessageTransform
{
    /**
     * @return array
     */
    function getTransformedMessage();


}