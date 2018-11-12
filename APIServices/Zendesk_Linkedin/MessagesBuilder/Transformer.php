<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder;
use Illuminate\Support\Facades\Log;

/**
 * Class Transformed
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder
 */
class Transformer extends MessageBuilder
{
    /**
     * @param $messages
     * @return array
     * @throws \Throwable
     */
    function getTransformedMessage($messages)
    {
        try {
            $messagesTransformed = $this->getFactoryMessage($messages);
            $newMessagesTransformed = $this->sortMessages($messagesTransformed);
            return $response = $this->trackingMessage($newMessagesTransformed);
        } catch (\Exception $exception) {
            Log::error("Transformed Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
            throw $exception;
        }
    }
}