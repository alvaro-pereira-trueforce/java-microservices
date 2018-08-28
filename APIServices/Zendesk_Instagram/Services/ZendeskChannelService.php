<?php

namespace APIServices\Zendesk_Instagram\Services;

use APIServices\Zendesk\Repositories\ChannelRepository;

class ZendeskChannelService
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

    public function registerNewChannelIntegration($data)
    {
        try {
            return $this->channelRepository->updateOrCreateChannelWithSettings($data, 'uuid', $data['settings']);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}