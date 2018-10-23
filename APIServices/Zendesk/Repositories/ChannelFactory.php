<?php

namespace APIServices\Zendesk\Repositories;

use Illuminate\Support\Facades\App;

class ChannelFactory
{
    /**
     * @param $channelServiceClass
     * @param $channelModel
     * @param array $params Must have the channelModel Class to instantiate the repository
     * @return mixed
     * @throws \Exception
     */
    public static function getChannelService($channelServiceClass, $channelModel, array $params = [])
    {
        try {
            static::configureChannelRepository($channelModel);
            return App::makeWith($channelServiceClass, $params);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public static function configureChannelRepository($channelModel)
    {
        App::when(ChannelRepository::class)->needs('$channelModel')->give(new $channelModel);
    }
}