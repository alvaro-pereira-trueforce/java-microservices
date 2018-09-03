<?php

namespace APIServices\Zendesk_Instagram\Services;

use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk\Services\IChannelService;
use APIServices\Zendesk\Services\ZendeskAPI;
use Illuminate\Support\Facades\App;

class ZendeskChannelService implements IChannelService
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
            throw $exception;
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
                throw new \Exception("The instagram account is already registered. Please use another instagram account or select a different facebook page.");
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
            throw $exception;
        }
    }
}