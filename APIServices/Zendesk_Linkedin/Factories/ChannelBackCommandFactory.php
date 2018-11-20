<?php

namespace APIServices\Zendesk_Linkedin\Factories;

use APIServices\Zendesk_Linkedin\Models\CommandTypes\ICommandType;
use APIServices\Zendesk_Linkedin\Models\CommandTypes\UnkonwnCommand;
use Illuminate\Support\Facades\App;

class ChannelBackCommandFactory
{
    /**
     * Get the correct Event Handler or Default
     * @param $command_name
     * @param $requestBody
     * @return
     */
    public static function getCommandHandler($command_name, $requestBody)
    {
        try {
            return App::makeWith('linkedin_' . $command_name, [
                'request_body' => $requestBody
            ]);
        } catch (\Exception $exception) {
            /** @var ICommandType $unknownCommand */
            return $unknownCommand = App::makeWith(UnkonwnCommand::class, ['request_body' => $requestBody]);

        }
    }

}