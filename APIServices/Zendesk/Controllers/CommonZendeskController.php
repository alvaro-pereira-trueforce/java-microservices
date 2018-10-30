<?php

namespace APIServices\Zendesk\Controllers;

use APIServices\Zendesk\Repositories\ChannelFactory;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestFactory;
use App\Repositories\ManifestRepository;
use App\Traits\ArrayTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;

abstract class CommonZendeskController extends Controller implements IZendeskController
{
    use ArrayTrait;

    /**
     * @var ManifestRepository This is the Manifest Repository
     */
    protected $manifest;
    protected $channelService;
    protected $zendeskInfo;

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

    protected $ticket_locales = [
        [
            'id' => 'en',
            'name' => 'English'
        ],
        [
            'id' => 'es',
            'name' => 'Español'
        ]
    ];

    /**
     * This is the name of the integration in the database must be equal than the database record
     * @var string $channel_name
     */
    protected $channel_name;

    public function __construct($channelServiceClassName, $channelModelClassName)
    {
        $this->manifest = ManifestFactory::getManifestRepository();
        try {
            $this->channelService = ChannelFactory::getChannelService($channelServiceClassName, $channelModelClassName);
            $this->zendeskInfo = $this->getZendeskInfoFromRequest(App::make(Request::class));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function getManifest(Request $request)
    {
        Log::notice("Zendesk Request: " . $request->method() . ' ' . $request->getPathInfo() . ' ' . $this->channel_name);
        return response()->json($this->manifest->getByName($this->channel_name));
    }

    /**
     * This function save automatically the account new or saved before on Redis database then return the basic frontend Variables.
     * @param Request $request
     * @param string $returnURL In case the frontend needed a Return URL for some social integrations.
     * @return array
     * @throws \Exception
     */
    protected function getAdminUIVariables(Request $request, $returnURL = '')
    {
        try {
            $frontend_variables = $this->getBasicBackendVariables($returnURL, $request->name);

            $accountInfo = $request->all();
            unset($accountInfo['state']);
            unset($accountInfo['metadata']);

            if (empty($this->zendeskInfo['metadata'])) {
                //This is the code when the user add an account.
                $accountID = Uuid::uuid4()->toString();
            } else {
                //This is the code for old users.
                $accountID = $this->zendeskInfo['metadata']['account_id'];
                $frontend_variables['backend_variables']['metadata'] = true;
                $accountInfo = array_merge($accountInfo, $this->zendeskInfo['metadata']);
            }

            $accountInfo['account_id'] = $accountID;
            $this->saveNewAccountInformation($accountID, $accountInfo);

            $frontend_variables['backend_variables']['account_id'] = $accountID;
            return $frontend_variables;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), ' Line: ' . $exception->getLine() . ' File: ' . $exception->getFile());
            throw $exception;
        }
    }

    /**
     * Helper to retrieve the metadata and state from the zendesk request
     * @param Request $request
     * @return array
     */
    protected function getZendeskInfoFromRequest(Request $request)
    {
        Log::debug('Request incoming, update Zendesk Info Variable..');
        Log::debug('Metadata:');
        Log::debug($request->metadata);
        Log::debug('State:');
        Log::debug($request->state);
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        $locale = $request->get('locale', '');
        return [
            'metadata' => $metadata,
            'state' => $state,
            'locale' => $locale
        ];
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
            Log::debug('Model to be saved in Redis:');
            Log::debug($newAccount);
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
            $accountInfo = json_decode(Redis::get($keyName), true);
            Log::debug('Model retrieved from Redis:');
            Log::debug($accountInfo);
            return $accountInfo;
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
    public function successReturn()
    {
        return response()->json('ok', 200);
    }
}