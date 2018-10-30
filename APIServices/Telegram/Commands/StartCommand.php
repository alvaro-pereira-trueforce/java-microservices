<?php

namespace APIServices\Telegram\Commands;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Utilities\StringUtilities;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Telegram\Services\ChannelService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /** @var TelegramService $telegramService */
    protected $telegramService;
    protected $channelSettings;

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

        try {
            $this->telegramService = App::make(TelegramService::class);

            $this->channelSettings = $this->telegramService->getChannelSettings();
            if (!empty($this->channelSettings) && !empty($this->channelSettings['locale'])) {
                App::setLocale($this->channelSettings['locale']);
            }
            $commandData = $this->telegramService->getStartedCommand($this->update);
            if (!$commandData) {
                //Make action
                $this->replyWithChatAction(['action' => Actions::TYPING]);
                // This will send a message using `sendMessage` method behind the scenes to
                // the user/chat id who triggered this command.
                // `replyWith<Message|Photo|Audio|VideoUpdate|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
                // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.

                if (array_key_exists('has_hello_message', $this->channelSettings) && (boolean)$this->channelSettings['has_hello_message'] == true && array_key_exists('hello_message', $this->channelSettings)) {
                    $this->replyWithMessage([
                        'text' => $this->channelSettings['hello_message'],
                        'reply_to_message_id' => $this->getUpdate()->getMessage()->getMessageId()
                    ]);
                }
                if (array_key_exists('required_user_info', $this->channelSettings) &&
                    (boolean)$this->channelSettings['required_user_info'] == true &&
                    !empty($this->update->getMessage()->getChat()->getType()) &&
                    $this->update->getMessage()->getChat()->getType() == 'private'
                ) {
                    $this->replyWithMessage([
                        'text' => trans('telegram.request_user_info_init_message')
                    ]);

                    $this->replyWithMessage([
                        'text' => trans('telegram.request_user_info_get_email_message')
                    ]);

                    $this->telegramService->setCommandProcess($this->update, $this->name, 'email');
                }
            } else {
                try {
                    $message = $this->update->getMessage()->getText();
                    switch ($commandData['state']) {
                        case 'email':
                            if (!$message || !StringUtilities::isValidEmail($message))
                                throw new \Exception(trans('telegram.request_user_info_error_email_message'));
                            $content = serialize(['email' => $message]);
                            $this->telegramService->setCommandProcess($this->update, $this->name, 'phone_number', $content);
                            $this->replyWithMessage([
                                'text' => trans('telegram.request_user_info_get_phone_message')
                            ]);
                            break;
                        case 'phone_number':
                            if (!$message || !StringUtilities::isValidPhoneNumber($message)) {
                                throw new \Exception(trans('telegram.request_user_info_error_phone_message'));
                            }

                            $content = unserialize($commandData['content']);
                            $content['phone_number'] = $message;

                            $this->replyWithMessage([
                                'text' => trans('telegram.request_user_info_success_message')
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
                                    'text' => trans('telegram.request_user_info_message_for_zendesk', ['email' => $content['email'], 'phone_number' => $content['phone_number']])
                                ]
                            ]);
                            $channel_service->sendUpdate($update, $this->telegram->getAccessToken());
                            $this->telegramService->cancelStartedCommand($this->update);
                            break;
                    }
                } catch (\Exception $exception) {
                    $this->replyWithMessage([
                        'text' => $exception->getMessage(),
                        'reply_to_message_id' => $this->getUpdate()->getMessage()->getMessageId()
                    ]);
                    $this->replyWithMessage([
                        'text' => trans('telegram.request_user_info_try_again')
                    ]);
                }
            }
        } catch (\Exception $exception) {
            Log::error("Telegram Command Error:");
            Log::error($exception->getMessage() . 'Line: ' . $exception->getLine() . $exception->getFile());
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

    protected function getAuthorExternalID($user_id, $user_username)
    {
        /** @var Utility $zendeskUtils */
        $zendeskUtils = App::make(Utility::class);
        return $zendeskUtils->getExternalID([$user_id, $user_username]);
    }
}