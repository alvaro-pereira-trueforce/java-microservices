<?php

namespace APIServices\Zendesk_Instagram\Services;

use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk\Services\ChannelService;
use APIServices\Zendesk\Services\ZendeskAPI;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ZendeskChannelService extends ChannelService
{
    /**
     * @var array
     */
    protected $state;

    /** @var ChannelRepository $channelRepository */
    protected $channelRepository;


    /**
     * ZendeskChannelService constructor.
     * @param ChannelRepository $channelRepository
     * @param array $state
     */
    public function __construct(ChannelRepository $channelRepository, $state = [])
    {
        $this->channelRepository = $channelRepository;
        $this->state = $state;
    }

    public function registerNewChannelIntegration(array $data)
    {
        try {
            return $this->channelRepository->updateOrCreateChannelWithSettings($data, 'uuid', $data['settings']);
        } catch (\Exception $exception) {
            Log::error("Database Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
            throw new \Exception("Something went wrong please close the pop up configuration and try again.");
        }
    }

    /**
     * Check if the instagram id has already registered in the database.
     * @param $instagram_id
     * @throws \Exception
     */
    public function checkIfInstagramIdIsAlreadyRegistered($instagram_id)
    {
        try {
            if ($this->channelRepository->checkIfExist('instagram_id', $instagram_id)) {
                throw new \Exception("This Instagram account is already registered. Please user another Instagram account or select a different Facebook page. If you want to delete old integrations from our database please uninstall the app and install it again, all accounts related to this domain will be erased.");
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param array $transformedMessages
     * @throws \Exception
     */
    public function sendUpdate(array $transformedMessages)
    {
        try {
            /** @var ZendeskAPI $zendeskAPI */
            $zendeskAPI = App::make(ZendeskAPI::class);
            $zendeskAPI->pushNewMessages($transformedMessages);
        } catch (\Exception $exception) {
            Log::error("Container Exception: " . $exception->getCode());
            Log::error($exception->getMessage());
        }
    }
}