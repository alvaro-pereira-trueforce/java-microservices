<?php

namespace APIServices\Zendesk\Services;


interface IChannelService
{
    public function sendUpdate(array $transformedMessage);

    public function registerNewChannelIntegration(array $data);
}