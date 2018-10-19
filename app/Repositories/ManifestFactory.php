<?php

namespace App\Repositories;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ManifestFactory
{
    public static function getManifestRepository()
    {
        try {
            return App::make(ManifestRepository::class);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return null;
        }
    }
}