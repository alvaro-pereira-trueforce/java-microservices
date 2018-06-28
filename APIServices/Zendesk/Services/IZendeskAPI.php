<?php

namespace APIServices\Zendesk\Services;


interface IZendeskAPI
{
    /**
     * @param array $message
     * @return array
     */
    function pushNewMessage($message);
}