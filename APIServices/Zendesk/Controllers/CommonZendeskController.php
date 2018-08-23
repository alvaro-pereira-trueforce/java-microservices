<?php

namespace APIServices\Zendesk\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class CommonZendeskController extends Controller implements IZendeskController
{
    protected $manifest;
    protected $service;

    /**
     * This is the name of the integration in the database must be equal than the database record
     * @var string $channel_name
     */
    protected $channel_name;

    public function __construct(ManifestRepository $repository)
    {
        $this->manifest = $repository;
    }

    protected function cleanArray($array)
    {
        return array_filter($array, function ($value) {
            return !empty($value);
        });
    }

    public function getManifest(Request $request)
    {
        Log::notice("Zendesk Request: " . $request->method() . ' ' . $request->getPathInfo());
        return response()->json($this->manifest->getByName($this->channel_name));
    }
}