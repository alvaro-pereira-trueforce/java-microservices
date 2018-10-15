<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;


/**
 * Class MessageType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
abstract class MessageType implements IMessageType
{

    /**
     * @param $message
     * @return mixed
     */
    public function getExternalIdUpdate($message)
    {
        return $message['companyStatusUpdate']['share']['id'];

    }

    /**
     * @param $message
     * @return mixed
     */
    public function getAuthorExternalIdUpdate($message)
    {
        return $message['company']['id'];
    }

    /**
     * @param $message
     * @return mixed
     */
    public function getAuthorNameUpdate($message)
    {
        return $message['company']['name'];
    }

    /**
     * @param $message
     * @return mixed
     */
    public function getBasicResponseUpdate($message)
    {
        return $message['companyStatusUpdate']['share']['comment'];

    }

    /**
     * @param $message
     * @return mixed
     */
    public function getDateUpdate($message)
    {
        return $message['companyStatusUpdate']['share']['timestamp'];

    }

    /**
     * @param $message
     * @return array
     */
    public function getAuthorInformationUpdate($message)
    {

        return [
            'external_id' => $this->getAuthorExternalIdUpdate($message),
            'name' => $this->getAuthorNameUpdate($message)
        ];
    }

}