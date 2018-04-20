<?php

namespace APIServices\Telegram\Console;

use APIServices\Telegram\Services\ChannelService;
use Illuminate\Console\Command;

class RemoveWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:removeWebhook {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove webhook manually for a telegram bot';

    /**
     * User repository to persist user in database
     *
     * @var ChannelService
     */
    protected $service;

    /**
     * Create a new command instance.
     *
     * @param  ChannelService $service
     * @return void
     */
    public function __construct(ChannelService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $result = $this->service->removeWebhook($this->argument('token'));

        $this->info($result);
    }
}