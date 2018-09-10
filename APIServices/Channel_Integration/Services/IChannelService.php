<?php

namespace APIServices\Channel_Integration\Services;

interface IChannelService
{
    /**
     * Get all Update Messages using Polling Method
     * @return array
     */
    public function getUpdates();

    /**
     * Send a channel back request
     *
     * @param string $parent_id Parent Identifier
     * @param string $message Message Text
     * @return string External Identifier
     * @throws \Exception
     */
    public function channelBackRequest($parent_id, $message);
}