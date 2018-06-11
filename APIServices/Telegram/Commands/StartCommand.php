<?php

namespace APIServices\Telegram\Commands;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Utilities\StringUtilities;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Services\ChannelService;
use Illuminate\Support\Facades\App;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Update;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $botFirstName = $this->getTelegram()->getMe()->getFirstName();
        /** @var TelegramService $service */
        $service = App::make(TelegramService::class);

        $commandData = $service->getStartedCommand($this->update);
        if (!$commandData) {
            //Make action
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            // This will send a message using `sendMessage` method behind the scenes to
            // the user/chat id who triggered this command.
            // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
            // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
            $this->replyWithMessage([
                'text' => 'Hello! Welcome I\'m the ' . $botFirstName . ' Bot, Do you need help? '
                    . json_decode('"\ud83d\ude0c"') .
                    ' Tell me and i\'ll send your question to the help desk support  ' . json_decode('"\ud83d\udcdc"') . json_decode('"\ud83d\udc4d\ud83c\udffb"')
                    . ', is it your first time?. I need some information before catch your request.',
                'reply_to_message_id' => $this->getUpdate()->getMessage()->getMessageId()
            ]);
            $this->replyWithMessage([
                'text' => 'Please provide us your email, phone number.'
            ]);
            $this->replyWithMessage([
                'text' => 'Enter your email:'
            ]);
            $service->setCommandProcess($this->update, $this->name, 'email');
        } else {
            try {
                $message = $this->update->getMessage()->getText();
                switch ($commandData['state']) {
                    case 'email':
                        if (!$message || !StringUtilities::isValidEmail($message))
                            throw new \Exception('Please enter a valid email address.');
                        $content = serialize(['email' => $message]);
                        $service->setCommandProcess($this->update, $this->name, 'phone_number', $content);
                        $this->replyWithMessage([
                            'text' => 'Great!, Enter your phone now:'
                        ]);
                        break;
                    case 'phone_number':
                        if (!$message || !StringUtilities::isValidPhoneNumber($message)) {
                            throw new \Exception('Please enter a valid phone number, your phone number must have 8 or more digits.');
                        }

                        $content = unserialize($commandData['content']);
                        $content['phone_number'] = $message;

                        $this->replyWithMessage([
                            'text' => 'Great!, Thank you. please send your question to let our support help you.'
                        ]);
                        /** @var ChannelService $channel_service */
                        $channel_service = App::make(ChannelService::class);
                        $update = new Update([
                            'update_id' => $this->update->getUpdateId(),
                            'message' => [
                                'message_id' => $this->update->getMessage()->getMessageId(),
                                'from' => $this->update->getMessage()->getFrom(),
                                'chat' => $this->update->getMessage()->getChat(),
                                'date' => $this->update->getMessage()->getDate(),
                                'text' => "Telegram channel: I'm starting to send messages, this is my info: email: ".$content['email']." Phone Number: ". $content['phone_number']
                            ]
                        ]);
                        $channel_service->sendUpdate($update, $this->telegram->getAccessToken());
                        $service->cancelStartedCommand($this->update);
                        break;
                }
            } catch (\Exception $exception) {
                $this->replyWithMessage([
                    'text' => $exception->getMessage(),
                    'reply_to_message_id' => $this->getUpdate()->getMessage()->getMessageId()
                ]);
                $this->replyWithMessage([
                    'text' => 'Try Again:'
                ]);
            }
        }

        // This will prepare a list of available commands and send the user.
        // First, Get an array of all registered commands
        // They'll be in 'command-name' => 'Command Handler Class' format.
        /*$commands = $this->getTelegram()->getCommands();

        // Build the list
        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        // Reply with the commands list
        $this->replyWithMessage(['text' => $response]);*/

//        if(env('APP_ENV') == 'production')
//        {
//            return;
//        }

//        $this->replyWithSticker([
//            'sticker' => 'CAADAgADqAUAAmMr4gleg7AbKyQ65gI',
//        ]);
        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        //$this->triggerCommand('subscribe');
    }

    protected function getAuthorExternalID($user_id, $user_username) {
        /** @var Utility $zendeskUtils */
        $zendeskUtils = App::make(Utility::class);
        return $zendeskUtils->getExternalID([$user_id, $user_username]);
    }
}