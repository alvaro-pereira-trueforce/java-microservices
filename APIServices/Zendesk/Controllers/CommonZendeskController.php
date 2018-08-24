<?php

namespace APIServices\Zendesk\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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

    /**
     * Get the basic array for all the integration channels to send information to the frontend
     * it uses the return URL if is needed by the authentication page and the integration name
     * @param $return_URL
     * @param $name
     * @return array
     */
    public function getBasicBackendVariables($return_URL, $name)
    {
        return [
            'backend_variables' => [
                'account_id' => null,
                'return_URL' => $return_URL,
                'name' => $name,
                'metadata' => null
            ]
        ];
    }

    /**
     * Save in REDIS the new account information with a time expiration
     * @param $newAccount
     * @param $keyName
     * @throws \Exception
     */
    public function saveNewAccountInformation($keyName, $newAccount)
    {
        try {
            Redis::set($keyName, json_encode($newAccount, true));
            //Expire in minutes
            Redis::expire($keyName, (30 * 60));
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Retrieve from REDIS the new account information
     * @param $keyName
     * @return array
     * @throws \Exception
     */
    public function getNewAccountInformation($keyName)
    {
        try {
            return json_decode(Redis::get($keyName), true);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function deleteNewAccountInformation($keyName)
    {
        try {
            Redis::del($keyName);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}