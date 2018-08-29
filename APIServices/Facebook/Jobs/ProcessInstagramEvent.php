<?php

namespace APIServices\Facebook\Jobs;

use APIServices\Zendesk_Instagram\Models\InstagramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessInstagramEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $instagramChannel;
    protected $field_id;
    protected $field_type;

    /**
     * Create a new job instance.
     * @param InstagramChannel $instagramChannel
     * @param string $field_id
     * @param string $field_type
     * @return void
     */
    public function __construct(InstagramChannel $instagramChannel, $field_id, $field_type)
    {
        $this->instagramChannel = $instagramChannel;
        $this->field_id = $field_id;
        $this->field_type = $field_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug($this->field_id);
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
