<?php

namespace APIServices\Facebook\Jobs;

use APIServices\Facebook\Models\Facebook;
use APIServices\Zendesk\Models\IMessageType;
use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk\Services\ZendeskAPI;
use APIServices\Zendesk\Services\ZendeskClient;
use APIServices\Zendesk_Instagram\Models\InstagramChannel;
use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use App\Traits\ArrayTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ProcessInstagramEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use ArrayTrait;

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
    public function __construct(InstagramChannel $instagramChannel, $field_type, $field_id)
    {
        $this->instagramChannel = $instagramChannel;
        $this->field_id = $field_id;
        $this->field_type = $field_type;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        Log::debug('Starting Job With: ' . $this->field_type . $this->field_id);
        try {
            App::when(ChannelRepository::class)->needs('$channelModel')->give(new InstagramChannel());
            $channelService = App::make(ZendeskChannelService::class);

            $settings = $this->instagramChannel->settings()->firstOrNew([])->toArray();
            $settings = $this->cleanArray($settings);

            //Configure Facebook API
            App::when(Facebook::class)
                ->needs('$access_token')
                ->give($this->instagramChannel->access_token);

            /** @var IMessageType $message */
            $message = App::makeWith('instagram_' . $this->field_type, [
                'field_id' => $this->field_id,
                'settings' => $settings
            ]);

            $transformedMessages = $message->getTransformedMessage();
            Log::debug($transformedMessages);

            if (!empty($transformedMessages)) {
                //Configure Zendesk API and Zendesk Client
                App::when(ZendeskClient::class)
                    ->needs('$access_token')
                    ->give($this->instagramChannel->zendesk_access_token);
                App::when(ZendeskAPI::class)
                    ->needs('$subDomain')
                    ->give($this->instagramChannel->subdomain);
                App::when(ZendeskAPI::class)
                    ->needs('$instance_push_id')
                    ->give($this->instagramChannel->instance_push_id);
                $channelService->sendUpdate($transformedMessages);
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
