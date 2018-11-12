<?php

namespace APIServices\Zendesk_Linkedin\Services;

use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk\Services\IChannelService;
use APIServices\Zendesk\Services\ZendeskAPI;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use APIServices\Zendesk\Services\ZendeskClient;

/**
 * Class ZendeskChannelService
 * @package APIServices\Zendesk_Linkedin\Services
 */
class ZendeskChannelService implements IChannelService
{
    /**
     * @var $state
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
     * Check if the Linkedin id has already registered in the database.
     * @param $company_id
     * @throws \Exception
     */
    public function checkIfLinkedIdIsAlreadyRegistered($company_id)
    {
        try {
            if ($this->channelRepository->checkIfExist('company_id', $company_id)) {
                throw new \Exception("This Company Page is already registered. Please use another Company Page or use a different LinkedIn Account. If you want to erase old integrations and be able to use them again please uninstall the app and install it again, all the LinkedIn Company Pages related to this domain will be erased.");
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     *
     * @param array $transformedMessage
     * @throws \Exception
     */
    public function sendUpdate(array $transformedMessage)
    {
        try {
            /** @var ZendeskAPI $zendeskAPI */
            $zendeskAPI = App::make(ZendeskAPI::class);
            $zendeskAPI->pushNewMessages($transformedMessage);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function registerNewChannelIntegration(array $data)
    {
        try {
            return $this->channelRepository->updateOrCreateChannelWithSettings($data, 'uuid', $data['settings']);
        } catch (\Exception $exception) {
            Log::error("Database Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
            throw new \Exception("A LinkedIn Company Page is needed.");
        }
    }

    /**
     * @param $metadata
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function getChannelIntegration($metadata)
    {
        try {
            return $this->channelRepository->getModelByColumnName('uuid', $metadata['account_id']);
        } catch (\Exception $exception) {
            Log::error("Database Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
            throw $exception;
        }
    }

    /**
     * @param $subdomain
     * @throws \Exception
     */
    public function deleteByZendeskSubdomain($subdomain)
    {
        try {
            $this->channelRepository->deleteAllByDomain($subdomain);
        } catch (\Exception $exception) {
            Log::error("Database Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
            throw $exception;
        }
    }

    /**
     * @param $account_id
     * @throws \Exception
     */
    public function deleteByZendeskIdIntegration($account_id)
    {
        try {
            $this->channelRepository->delete($account_id);
        } catch (\Exception $exception) {
            Log::error("Database Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
            throw $exception;
        }
    }


    /**
     * @param $company_id
     * @return mixed
     * @throws \Exception
     */
    public function getModelFromLinkedInIntegration($company_id)
    {
        try {
            $zendeskCreateDate = $this->channelRepository->getModelByColumnName('company_id', $company_id);
            return $response = $zendeskCreateDate;
        } catch (\Exception $exception) {
            Log::error("Database Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
            throw $exception;
        }

    }

    /**
     * Configure Dependency Container to use the Zendesk Access Token Domain and Instance Push ID
     * @param $zendesk_access_token
     * @param $subdomain
     * @param $instance_push_id
     */
    public function configureZendeskAPI($zendesk_access_token, $subdomain, $instance_push_id)
    {
        try {
            App::when(ZendeskClient::class)
                ->needs('$access_token')
                ->give($zendesk_access_token);
            App::when(ZendeskAPI::class)
                ->needs('$subDomain')
                ->give($subdomain);
            App::when(ZendeskAPI::class)
                ->needs('$instance_push_id')
                ->give($instance_push_id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

}