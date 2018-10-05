<?php

namespace APIServices\Zendesk\Controllers;

use APIServices\Zendesk\Models\EventsTypes\IEventType;
use APIServices\Zendesk\Models\EventsTypes\UnknownEvent;
use APIServices\Zendesk\Repositories\ChannelRepository;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use App\Traits\ArrayTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

abstract class CommonZendeskController extends Controller implements IZendeskController
{
    use ArrayTrait;

    protected $manifest;
    protected $channelService;


    protected $ticket_types = [
        ['id' => 'problem',
            'name' => 'Problem'],
        ['id' => 'incident',
            'name' => 'Incident'],
        ['id' => 'question',
            'name' => 'Question'],
        ['id' => 'task',
            'name' => 'Task']
    ];

    protected $ticket_priorities = [
        ['id' => 'urgent',
            'name' => 'Urgent'],
        ['id' => 'high',
            'name' => 'High'],
        ['id' => 'normal',
            'name' => 'Normal'],
        ['id' => 'low',
            'name' => 'Low'],
    ];

    /**
     * This is the name of the integration in the database must be equal than the database record
     * @var string $channel_name
     */
    protected $channel_name;

    public function __construct(ManifestRepository $repository, $channelService, $channelModel)
    {
        $this->manifest = $repository;
        try {
                $this->channelService = $this->getChannelService($channelService, $channelModel);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public
    function getManifest(Request $request)
    {
        Log::notice("Zendesk Request: " . $request->method() . ' ' . $request->getPathInfo() . ' ' . $this->channel_name);
        return response()->json($this->manifest->getByName($this->channel_name));
    }

    /**
     * Get the basic array for all the integration channels to send information to the frontend
     * it uses the return URL if is needed by the authentication page and the integration name
     * @param $return_URL
     * @param $name
     * @return array
     */
    public
    function getBasicBackendVariables($return_URL, $name)
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
    public
    function saveNewAccountInformation($keyName, $newAccount)
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
    public
    function getNewAccountInformation($keyName)
    {
        try {
            return json_decode(Redis::get($keyName), true);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Delete the NewAccount Information from REDIS before its expiration. (Clean up)
     * @param $keyName
     * @throws \Exception
     */
    public
    function deleteNewAccountInformation($keyName)
    {
        try {
            Redis::del($keyName);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Return Ok with status 200
     * @return \Illuminate\Http\JsonResponse
     */
    public
    function successReturn()
    {
        return response()->json('ok', 200);
    }

    /**
     * Get the correct Event Handler or Default
     * @param $event_name
     * @param $event_data
     * @return IEventType
     */
    protected
    function getEventHandler($event_name, $event_data)
    {
        try {
            return App::makeWith($event_name, [
                'data' => $event_data
            ]);
        } catch (\Exception $exception) {
            return App::makeWith(UnknownEvent::class, [
                'data' => $event_data
            ]);
        }
    }


    /**
     * @param $channelServiceClass
     * @param $channelModel
     * @param array $params Must have the channelModel Class to instantiate the repository
     * @return mixed
     * @throws \Exception
     */
    protected
    function getChannelService($channelServiceClass, $channelModel, array $params = [])
    {
        try {
            $this->configureChannelRepository($channelModel);
            return App::makeWith($channelServiceClass, $params);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    protected
    function configureChannelRepository($channelModel)
    {
        App::when(ChannelRepository::class)->needs('$channelModel')->give(new $channelModel);
    }
}