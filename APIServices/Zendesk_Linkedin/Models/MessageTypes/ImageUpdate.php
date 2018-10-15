<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;


/**
 * Class ImageUpdate
 * This class will retrieve a ImageUpdate Model class
 */
class ImageUpdate extends MessageType
{

    /**
     * @param $data
     * @return mixed|void
     */
    function getTransformedMessage($data)
    {
        return $data['updateContent'];


    }
}