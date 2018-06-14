<?php

namespace APIServices\Telegram\Console;

use APIServices\Zendesk_Telegram\Repositories\TelegramRepository;
use Illuminate\Console\Command;

class AddBotCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:add {token} {zendesk_domain} {integration_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds new bot by token to the database (Test Propose)';

    /**
     * User repository to persist user in database
     *
     * @var TelegramRepository
     */
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @param  TelegramRepository $repository
     * @return void
     */
    public function __construct(TelegramRepository $repository) {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $model = $this->repository->create([
            'token' => $this->argument('token'),
            'zendesk_app_id' => $this->argument('zendesk_domain'),
            'integration_name' => $this->argument('integration_name')
        ]);

        $this->info(sprintf('A record was created with ID %s', $model->uuid));
    }
}