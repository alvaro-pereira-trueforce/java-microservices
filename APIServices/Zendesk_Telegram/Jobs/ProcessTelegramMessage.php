<?php

namespace APIServices\Zendesk_Telegram\Jobs;


use APIServices\Zendesk_Telegram\Models\TelegramServiceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use APIServices\Zendesk_Telegram\Services\ChannelService;
use Telegram\Bot\Objects\Update;

class ProcessTelegramMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Update */
    protected $update;

    protected $token;

    public function __construct($update, $token)
    {
        $this->update = $update;
        $this->token = $token;
    }

    /**
     * Execute the job.
     * @param ChannelService $channelService
     * @param Api $telegramAPI
     * @return void
     * @throws \Exception
     */
    public function handle(ChannelService $channelService, Api $telegramAPI)
    {
        // This fetch webhook update + processes the update through the commands system.
        Log::debug($this->update);
        $telegramAPI->setAccessToken($this->token);
        TelegramServiceFactory::configureTelegramService($telegramAPI);
        try {
            $commandData = $channelService->getStartedCommand($this->update);
            if (!$channelService->isCommand($this->update) && $commandData) {
                $channelService->triggerCommand($commandData['command'], $commandData['state'], $this->update);
            }
            if (!$channelService->isCommand($this->update) && !$commandData) {

                $channelService->sendUpdate($this->update, $this->token);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
    }
}