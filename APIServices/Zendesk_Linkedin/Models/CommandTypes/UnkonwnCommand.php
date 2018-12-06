<?php


namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;


use Illuminate\Support\Facades\Log;

/**
 * Class UnkonwnCommand
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
class UnkonwnCommand extends CommandType
{
    /**
     *
     * @throws \Exception
     */
    function handleCommand()
    {
        Log::debug('Command no valid' . $this->nameCommand);
        $response = $this->getZendeskDefaultModel('The following is not a valid command');
        $this->getZendeskAPIServiceInstance()->pushNewMessages($response);
    }
}