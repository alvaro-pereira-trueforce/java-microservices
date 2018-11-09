<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder\MessageFilter;

use Illuminate\Support\Facades\Log;

/**
 * Class Comment
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder\MessageFilter
 */
class Comment extends MessageFilter
{
    /**
     * @param $channelBackMessage
     * @return string
     * @throws \Exception
     */
    function getTransformedMessage($channelBackMessage)
    {
        try {
            foreach ($this->comment['updateComments']['values'] as $message) {
                if ($message['comment'] == $channelBackMessage) {
                    $response = strval($message['id']);
                    return $response;
                }
            }
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
            throw $exception;

        }
    }
}