<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;


/**
 * Class Image
 * This class will retrieve a Image Model class
 */
class Image extends MessageType
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