<?php

namespace App\Storage;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class StorageHelper
 * @package App\Storage
 */
class StorageHelper
{

    /**
     * @param $keyName
     * @param $data
     */
    static function saveDataToRedis($keyName, $data)
    {
        try {
            Redis::set($keyName, json_encode($data, true));
            //Expire in minutes
            Redis::expire($keyName, 30 * 60);
        } catch (\Exception $exception) {
            Log::error('Redis save error:');
            Log::error($exception->getMessage());
        }
    }

    /**
     * @param $keyName
     * @return array
     */
    static function getDataToRedis($keyName)
    {
        try {
            $data = json_decode(Redis::get($keyName), true);
            return $data;
        } catch (\Exception $exception) {
            Log::error('Redis save error:');
            Log::error($exception->getMessage());
            return [];
        }
    }
}