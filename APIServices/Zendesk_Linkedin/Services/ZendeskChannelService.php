<?php

namespace APIServices\Zendesk_Linkedin\Services;


use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk\Services\IChannelService;
use APIServices\Zendesk\Services\ZendeskAPI;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

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

    /**
     * Check if the instagram id has already registered in the database.
     * @param $company_id
     * @throws \Exception
     */
    public function checkIfLinkedIdIsAlreadyRegistered($company_id)
    {
        try {
            if ($this->channelRepository->checkIfExist('company_id', $company_id)) {
                throw new \Exception("This Company account is already registered. Please user another Company account or select a different Linked page. If you want to delete old integrations from our database please uninstall the app and install it again, all accounts related to this domain will be erased.");
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function sendUpdate(array $transformedMessage)
    {
        // TODO: Implement sendUpdate() method.
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
}