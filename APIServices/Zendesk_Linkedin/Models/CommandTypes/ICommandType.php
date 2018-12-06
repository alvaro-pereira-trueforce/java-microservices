<?php

namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;


/**
 * Interface ICommandType
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
interface ICommandType
{
    /**
     * @return string
     */
    function handleCommand();

}