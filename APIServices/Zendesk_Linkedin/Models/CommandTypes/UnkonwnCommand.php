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
        try {
            Log::debug('Command no valid' . $this->nameCommand);
            $response = $this->getZendeskModel('
            You can use the following LinkedIn commands:
            s@getlist
            s@getcompany
            s@getcompany
            s@getstatistics
            s@getstatistics_count
            s@getstatistics_functions
            s@getstatistics_seniorities
            s@getstatistics_countries
            The following is not a valid command');
            $this->getZendeskAPIServiceInstance()->pushNewMessage($response);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}