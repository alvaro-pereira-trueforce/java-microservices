<?php

namespace APIServices\Zendesk_Telegram\Controllers;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk\Controllers\CommonZendeskController;
use APIServices\Zendesk_Telegram\Models\EventsTypes\IEventType;
use APIServices\Zendesk_Telegram\Models\EventsTypes\UnknownEvent;
use APIServices\Zendesk_Telegram\Services\ChannelService;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use JavaScript;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class ZendeskController extends CommonZendeskController
{
    protected $channel_service;
    protected $telegram_service;

    public function __construct(ManifestRepository $repository, TelegramService $service, ChannelService $channelService, Request $request)
    {
        $this->manifest = $repository;
        $this->telegram_service = $service;
        $this->channel_service = $channelService;
        $this->zendeskInfo = $this->getZendeskInfoFromRequest($request);
    }

    public function getManifest(Request $request)
    {
        Log::notice("Zendesk Request: " . $request->method() . ' ' . $request->getPathInfo());
        return response()->json($this->manifest->getByName('Telegram Channel'));
    }

    public function admin_UI(Request $request)
    {
        /* Example{
            'name' => NULL,
            'metadata' => NULL,
            'state' => NULL,
            'return_url' => 'https://d3v-assuresoft.zendesk.com/zendesk/channels/integration_service_instances/editor_finalizer',
            'instance_push_id' => '3cd1d2a5-aaf2-41fe-a9f1-519499605854',
            'zendesk_access_token' => '601444c6f97f74d331bdc5fb8843b245f16079511759581582227c5643771588',
            'subdomain' => 'd3v-assuresoft',
            'locale' => 'en-US',
        }*/
        try {
            $instance_push_id = $request->instance_push_id;
            $zendesk_access_token = $request->zendesk_access_token;
            if (!$zendesk_access_token || !$instance_push_id) {
                JavaScript::put([
                    'backend_variables' => [
                        'pull_mode' => true
                    ]
                ]);
            }
            $telegramAccessToken = '';
            if (!empty($this->zendeskInfo['metadata']) && !empty($this->zendeskInfo['metadata']['token'])) {
                $savedAccount = $this->telegram_service->getById($this->zendeskInfo['metadata']['token']);
                $settings = $savedAccount->settings()->first();
                if (empty($settings) || empty($savedAccount)) {
                    JavaScript::put([
                        'backend_variables' => [
                            'pull_mode' => true
                        ]
                    ]);
                    return view('telegramV2.admin_ui');
                }
                $this->zendeskInfo['metadata']['account_id'] = $savedAccount->uuid;
                unset($this->zendeskInfo['metadata']['token']);
                $telegramAccessToken = $savedAccount->token;
            }

            $front_end_variables = $this->getAdminUIVariables($request);
            $front_end_variables['backend_variables']['token'] = $telegramAccessToken;
            if (!empty($settings)) {
                $front_end_variables['backend_variables']['settings'] = $settings->toArray();
                if (!empty($settings['tags'])) {
                    $front_end_variables['backend_variables']['settings']['tags'] = implode(' ', json_decode($settings['tags'], true));
                }
                $front_end_variables['backend_variables']['settings']['has_hello_message'] = (bool)$front_end_variables['backend_variables']['settings']['has_hello_message'];
                $front_end_variables['backend_variables']['settings']['required_user_info'] = (bool)$front_end_variables['backend_variables']['settings']['required_user_info'];
            }
            $front_end_variables['backend_variables']['ticket_types'] = $this->ticket_types;
            $front_end_variables['backend_variables']['ticket_priorities'] = $this->ticket_priorities;
            JavaScript::put($front_end_variables);
            return view('telegramV2.admin_ui');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }
    }

    public function admin_ui_validate_data(Request $request)
    {
        try {
            $data = $request->all();
            /**
             * Example request[
             * "account_id" => "191ed66c-4ed4-4618-baad-17c8d30ec249"
             * "name" => "My Clients From Instagram"
             * "token" => "575219012:AAEc-eJkXADjeiBT3-lymQ_yP6MsLK1rL14"
             * "settings" => array:4 [
             * "has_hello_message" => false
             * "required_user_info" => false
             * "selected_ticket_type" => "incident"
             * "tags" => "telegram channel users"
             * ]
             * ]
             */

            $account_id = $request->account_id;
            $name = $request->name;
            $token = $request->token;

            $account = $this->getNewAccountInformation($account_id);

            $telegramBot = $this->telegram_service->checkValidTelegramBot($token);
            if (!$telegramBot) {
                $errors = 'Invalid token, use Telegram Bot Father to create one.';
                return response()->json(['message' => $errors], 404);
            }

            try {
                $savedAccount = $this->telegram_service->getById($account_id);
            } catch (\Exception $exception) {
                if ($this->telegram_service->isTokenRegistered($token)) {
                    $errors = 'That telegram token is already registered.';
                    return response()->json(['message' => $errors], 404);
                }

                if ($this->telegram_service->isNameRegistered($account['subdomain'], $name)) {
                    $errors = 'That integration name is already registered.';
                    return response()->json(['message' => $errors], 404);
                }
            }

            $telegramResponse = $this->telegram_service->setWebhook($token);

            if (!$telegramResponse || !$telegramResponse[0]) {
                $errors = 'There was an error with bots configuration, please contact support.';
                return response()->json(['message' => $errors], 404);
            }

            if (!empty($data['settings']['tags'])) {
                $tags = json_encode(explode(' ', $data['settings']['tags']), true);
                $data['settings']['tags'] = $tags;
            }
            $return_url = $account['return_url'];
            unset($account['return_url']);
            $account = array_merge($account, $data);
            $account['uuid'] = $account['account_id'];
            unset($account['account_id']);
            $account['integration_name'] = $account['name'];
            unset($account['name']);
            $account['zendesk_app_id'] = $account['subdomain'];
            unset($account['subdomain']);

            $metadata = $this->telegram_service->registerNewIntegration($account);
            if (!$metadata) {
                $errors = 'There was an error processing your data, please check your information or contact support.';
                return response()->json(['message' => $errors], 404);
            }
            $metadata['return_url'] = $return_url;
            Log::debug(json_encode($metadata));
            $this->saveNewAccountInformation($account_id, $metadata);
            return response()->json([
                'save_url' => env('APP_URL') . '/telegram/admin_ui_save/' . $account_id
            ], 200);

        } catch (\Exception $exception) {
            Log::error($exception);
            return 'Please try again, if the problem persists please contact our Support team zendesk@assuresoft.com';
        }
    }

    public function admin_ui_save($account_id)
    {
        try {
            $account = $this->getNewAccountInformation($account_id);
            $this->deleteNewAccountInformation($account_id);
            $return_url = $account['return_url'];
            unset($account['return_url']);
            $account = $this->cleanArray($account);
            Log::debug($account);
            return view('post_metadata', ['return_url' => $return_url, 'name' => $account['integration_name'], 'metadata' => json_encode($account)]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }
    }

    public function pull(Request $request)
    {
        Log::debug($request);
        $updates = $this->channel_service->getUpdates();
        $response = [
            'external_resources' => $updates,
            'state' => ""
        ];
        Log::debug(json_encode($response));
        return response()->json($response);
    }

    public function channel_back(Request $request)
    {
        try {
            $external_id = $this->channel_service->channelBackRequest($request->parent_id, $request->message);

            $response = [
                'external_id' => $external_id
            ];
            return response()->json($response);
        } catch (\Exception $exception) {
            throw new ServiceUnavailableHttpException($exception->getMessage());
        }
    }

    public function click_through(Request $request)
    {
        Log::info($request->all());
    }

    public function health_check(Request $request)
    {
        return $this->successReturn();
    }

    public function event_callback(Request $request)
    {
        //Log::info("Event On Zendesk: \n" . $request->getContent() . "\n");
        if ($request->has('events') && $request->events[0] && array_key_exists('type_id', $request->events[0])) {
            $data = [
                'type_id' => $request->events[0]['type_id'],
                'integration_name' => $request->events[0]['integration_name'],
                'info' => $request->events[0]['data'],
                'subdomain' => $request->events[0]['subdomain']
            ];
            try {
                /** @var IEventType $event */
                $event = App::makeWith($request->events[0]['type_id'], [
                    'data' => $data
                ]);
            } catch (\Exception $exception) {
                /** @var IEventType $event */
                $event = App::makeWith(UnknownEvent::class, [
                    'data' => $data
                ]);
            }
            $event->handleEvent();
        }
        return $this->successReturn();
    }

    public function successReturn()
    {
        return response()->json('ok', 200);
    }
}
