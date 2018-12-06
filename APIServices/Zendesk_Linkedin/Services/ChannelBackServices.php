<?php


namespace APIServices\Zendesk_Linkedin\Services;


use APIServices\LinkedIn\Services\LinkedinService;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Linkedin\Factories\ChannelBackCommandFactory;
use APIServices\Zendesk_Linkedin\MessagesBuilder\MessageFilter\Comment;
use APIServices\Zendesk_Linkedin\Models\CommandTypes\ICommandType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class ChannelBackServices
 * @package APIServices\Zendesk_Linkedin\Services
 */
class ChannelBackServices
{
    /**
     * @var $request
     */
    protected $request;
    /**
     * @var LinkedinService
     */
    protected $linkedinService;
    /**
     * @var Utility
     */
    protected $utility;
    /**
     * @var $metadata
     */
    protected $metadata;


    /**
     * ChannelBackServices constructor.
     * @param $request
     * @param LinkedinService $linkedinService
     */
    public function __construct($request, LinkedinService $linkedinService)
    {
        $this->request = $request;
        $this->linkedinService = $linkedinService;
        $this->metadata = json_decode($request->metadata, true);
    }

    /**
     * @param $message
     * @return string
     * @throws \Exception
     */
    public function getChannelBackRequest($message)
    {
        Log::debug($this->isLinkedinCommand($message));
        if ($this->isLinkedinCommand($message)) {
            /** @var ICommandType $eventLinkedInCommand */
            $requestBody['nameCommand'] = $message;
            $requestBody['body'] = $this->request;
            $eventLinkedInCommand = ChannelBackCommandFactory::getCommandHandler($message, $requestBody);
            $eventLinkedInCommand->handleCommand();
            $channelBackId = $this->request->thread_id;
            return $channelBackId;
        } else {
            $this->sendCommentLinkedIn($this->request);
            $channelBackId = $this->getExternalIdResponse($this->request);
            return $channelBackId;
        }
    }

    /**
     * @param $message
     * @return bool
     */
    public function isLinkedinCommand($message)
    {
        try {
            preg_match('/^s@get+/', $message, $commands);
            if (!empty($commands)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
    }
    /**
     * @param $request
     * @throws \Exception
     */
    public function sendCommentLinkedIn($request)
    {
        try {
            $requestParameter = $this->getLinkedInUpdateKey($request->thread_id);
            $this->linkedinService->postLinkedInComment($request, $requestParameter);
            Log::debug('post in LinkedIn success');
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $thread_id
     * @return string
     */
    public function getLinkedInUpdateKey($thread_id)
    {
        $response = explode(':', $thread_id);
        return $response['2'];
    }

    /**
     * @param $request
     * @return string
     * @throws \Exception
     */
    public function getExternalIdResponse($request)
    {
        try {
            $requestBody['thread_id'] = $request->thread_id;
            $requestBody['access_token'] = $this->metadata['access_token'];
            $channelBackComment = App::makeWith(Comment::class, ['metadata' => $requestBody]);
            $channelBackId = $channelBackComment->getTransformedMessage($request->message);
            return $channelBackId;
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

}