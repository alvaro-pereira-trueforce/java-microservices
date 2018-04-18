<?php

namespace App\Console;

use App\Repositories\ManifestRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddApplicationManifestCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zendesk:add_application_manifest {name} {id} {author} {version}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a application manifest to publish on zendesk';

    /**
     * User repository to persist user in database
     *
     * @var ManifestRepository
     */
    protected $manifestRepository;

    /**
     * Create a new command instance.
     *
     * @param  ManifestRepository $manifestRepository
     * @return void
     */
    public function __construct(ManifestRepository $manifestRepository) {
        parent::__construct();

        $this->manifestRepository = $manifestRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $app_url = env('APP_URL') . '/';
        $admin_ui = $this->ask('What is the admin UI URL?');
        $pull_url = $this->ask('What is the pull URL?');
        $channelback_url = $this->ask('What is the channel back URL?');
        $clickthrough_url = $this->ask('What is the click through URL?');
        $healthcheck_url = $this->ask('What is the health check URL?');

        DB::beginTransaction();
        try
        {
            $manifest = $this->manifestRepository->createManifestWithUrls([
                'name' => $this->argument('name'),
                'id' => $this->argument('id'),
                'author' => $this->argument('author'),
                'version' => $this->argument('version')
            ],
                [
                    'admin_ui' => $admin_ui ? $app_url . $admin_ui : '',
                    'pull_url' => $pull_url ? $app_url . $pull_url : '',
                    'channelback_url' => $channelback_url ? $app_url . $channelback_url : '',
                    'clickthrough_url' => $clickthrough_url ? $app_url . $clickthrough_url : '',
                    'healthcheck_url' => $healthcheck_url ? $app_url . $healthcheck_url : ''
                ]);
            DB::commit();
            $this->info(sprintf('A new application manifest was created with ID %s', $manifest));
        }catch (\Exception $exception)
        {
            DB::rollBack();
            $this->info(sprintf('There is an error %s', $exception->getMessage()));
        }

    }
}