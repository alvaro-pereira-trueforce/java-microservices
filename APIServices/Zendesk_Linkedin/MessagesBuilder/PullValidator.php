<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder;

/**
 * Class PullValidator
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder
 */
class PullValidator extends MessageBuilder
{
    /**
     * @param $messages
     * @return array
     * @throws \Exception
     */
    function getTransformedMessage($messages)
    {
        try {
            $response=[];
            $limitPull=$messages['limitPull'];
            foreach ($messages['transformedMessages'] as $message) {
                if ($message['created_at'] > $limitPull) {
                    $response[] = $message;
                }
            }
            return $response;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}