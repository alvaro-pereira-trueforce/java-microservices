<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder;

use APIServices\LinkedIn\Services\LinkedinService;
use Illuminate\Support\Facades\Log;
/**
 * Class TransformMessageSearcher
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder
 */
class TransformMessageSearcher
{
    /**
     * @var $linkedinService
     */
    protected $linkedinService;

    /**
     * @var $params
     */
    protected $params;

    /**
     * @var array $listComments
     */
    protected $listComments;

    /**
     * TransformMessageSearcher constructor.
     * @param $params
     * @param LinkedinService $linkedinService
     * @throws \Exception
     */
    public function __construct($params, LinkedinService $linkedinService)
    {
        $this->linkedinService = $linkedinService;
        $this->params = $params;
        $this->listComments = $this->linkedinService->getAllPostChannelBackFormat($params);
    }

    /**
     * @param $channelBackMessage
     * @return string
     * @throws \Exception
     */
    public function searchCommentByMessage($channelBackMessage)
    {
        try {
            foreach ($this->listComments['updateComments']['values'] as $message) {
                if ($message['comment'] == $channelBackMessage) {
                  $response=strval($message['id']);
                    return $response;
                }
            }
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
            throw $exception;

        }
    }

}