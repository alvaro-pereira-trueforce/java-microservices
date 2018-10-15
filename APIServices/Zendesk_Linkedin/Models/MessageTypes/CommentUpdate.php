<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;


use Illuminate\Support\Facades\Log;

/**
 * Class CommentType
 * This class will retrieve a comment Model class
 */
class CommentUpdate extends MessageType
{

    /**
     * @param $messages
     * @return array|mixed
     */
    function getTransformedMessage($messages)
    {
        $pila=[];
    foreach ($messages as $message => $item){
            Log::debug($message);

    }

        return $pila;
    }

}