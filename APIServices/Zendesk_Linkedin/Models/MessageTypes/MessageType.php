<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;


use APIServices\Zendesk\Utility;

/**
 * Class MessageType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
abstract class MessageType implements IMessageType
{
    /**
     * @var Utility
     */
    protected $zendeskUtils;


    /**
     * MessageType constructor.
     * @param Utility $zendeskUtils
     */
    public function __construct(Utility $zendeskUtils)
    {
        $this->zendeskUtils = $zendeskUtils;

    }

    /**
     * @param $message
     * @return mixed
     */
    public function getExternalId($message)
    {

        return $message['companyStatusUpdate']['share']['id'];


    }

    /**
     * @param $message
     * @return mixed
     */
    public function getAuthorExternalId($message)
    {
        return $message['company']['id'];
    }

    /**
     * @param $message
     * @return mixed
     */
    public function getAuthorName($message)
    {
        return $message['company']['name'];
    }

    /**
     * @param $message
     * @return mixed
     */
    public function getBasicResponse($message)
    {
        return $message['companyStatusUpdate']['share']['comment'];

    }
    /**
     * @param $message
     * @return mixed
     */
    public function getCreationDate($message)
    {
        return $message['companyStatusUpdate']['share']['timestamp'];

    }
}