<?php

namespace APIServices\Zendesk_Linkedin\Services;


use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk\Services\IChannelService;
use APIServices\Zendesk_Linkedin\Models\MessageTypes\Comment;
use APIServices\Zendesk_Linkedin\Models\MessageTypes\Image;
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
                throw new \Exception("This Company Page is already registered. Please use another Company Page or use a different LinkedIn Account. If you want to erase old integrations and be able to use them again please uninstall the app and install it again, all the LinkedIn Company Pages related to this domain will be erased.");
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
            throw new \Exception("A LinkedIn Company Page is needed.");
        }
    }

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
     * @param array $messages
     * @return mixed
     */
    public function getFactoryMessageType(array $messages)
    {
        //Log::debug(collect($messages));
        try {
            foreach ($messages as $message => $item) {
                if (!empty($messages['values'])) {
                    foreach ($messages['values'] as $id => $index) {
                        if (!empty($messages['values'][$id]['updateContent'])) {
                            foreach ($messages['values'][$id]['updateContent']['companyStatusUpdate']['share'] as $updateMessage => $index) {
                                if (array_key_exists('content', $messages['values'][$id]['updateContent']['companyStatusUpdate']['share'])) {
                                    $imageModel = App::make(Image::class);
                                    return $imageModel->getTransformedMessage($messages['values'][$id]['updateContent']);
                                } else {
                                    $commentModel = App::make(Comment::class);
                                    return $commentModel->getTransformedMessage($messages['values'][$id]['updateContent']);
                                }
                            }
                        } else {
                            Log::error("Transformed Error: ");
                        }
                    }
                } else {
                    Log::error("Transformed Error: ");
                }
            }
        }catch (\Exception $exception){
            Log::error("Transformed Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
        }

 }

}