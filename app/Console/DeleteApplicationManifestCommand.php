<?php

namespace App\Console;

use App\Repositories\ManifestRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteApplicationManifestCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zendesk:delete_application_manifest {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an application manifest using the primary key';

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
        DB::beginTransaction();
        try {
            $result = $this->manifestRepository->delete($this->argument('id'));
            DB::commit();
            if($result == 1)
            {
                $this->info(sprintf('The record was deleted %s', $result));
            }
            else
            {
                $this->info('There is no records with that ID');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->info(sprintf('There is an error %s', $exception->getMessage()));
        }

    }
}