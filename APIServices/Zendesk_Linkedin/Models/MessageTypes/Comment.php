<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;


/**
 * Class Comment
 * This class will retrieve a comment Model class
 */
class Comment extends MessageType
{
    /**
     * @param $message
     * @return mixed
     */
    function getTransformedMessage($message)
    {
        $updateMessage['external_id']=$this->getExternalId($message);
        $updateMessage['message']=$this->getBasicResponse($message);
        $updateMessage['created_at']=$this->getCreationDate($message);
        $updateMessage['autor'];

    }
}