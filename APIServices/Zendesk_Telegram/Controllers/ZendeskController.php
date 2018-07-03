<?php

namespace APIServices\Zendesk_Telegram\Controllers;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk_Telegram\Models\EventsTypes\IEventType;
use APIServices\Zendesk_Telegram\Models\EventsTypes\UnknownEvent;
use APIServices\Zendesk_Telegram\Services\ChannelService;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class ZendeskController extends Controller
{

    protected $manifest;

    public function __construct(ManifestRepository $repository)
    {
        $this->manifest = $repository;
    }

    public function getManifest(Request $request)
    {
        Log::notice("Zendesk Request: " . $request->method() . ' ' . $request->getPathInfo());
        return response()->json($this->manifest->getByName('Telegram Channel'));
    }

    public function adminUI(Request $request, TelegramService $service)
    {
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        //$locale = $request->locale;
        $instance_push_id = $request->instance_push_id;
        $zendesk_access_token = $request->zendesk_access_token;
        $data = [
            'return_url' => $request->return_url,
            'subdomain' => $request->subdomain,
            'name' => $request->name,
            'submitURL' => env('APP_URL') . '/telegram/admin_ui_add',
            'token' => '',
            'has_hello_message' => false,
            'required_user_info' => true,
            'hello_message' => null,
            'ticket_type' => null,
            'ticket_priority' => null,
            'tags' => null
        ];

        $data['token_hide'] = false;

        if (!$zendesk_access_token || !$instance_push_id) {
            $zendesk_access_token = '';
            $instance_push_id = '';
            $data['pull_mode'] = true;
        }

        try {
            if (!$metadata) {
                //This would only happen when the integration has push enabled functionality in the domain saved manifest
                $newRecord = $service->setAccountRegistration([
                    'zendesk_access_token' => $zendesk_access_token,
                    'instance_push_id' => $instance_push_id,
                    'zendesk_app_id' => $data['subdomain']
                ]);
                if (!$newRecord || empty($newRecord))
                    throw new \Exception('There was an error');
            } else {
                $data['token_hide'] = true;
                $data['submitURL'] = env('APP_URL') . '/telegram/admin_ui_edit';
                $token = $service->getById($metadata['token']);
                $data['token'] = $token->token;
                $settings = $service->getChannelSettings();
                if(empty($settings))
                {
                    return view('telegram.admin_ui_old_users_without_settings', $data);
                }
                $data['has_hello_message'] = (boolean)$settings['has_hello_message'];
                $data['required_user_info'] = (boolean)$settings['required_user_info'];
                $data['hello_message'] = $settings['hello_message'];
                $data['ticket_type'] = $settings['ticket_type'];
                $data['ticket_priority'] = $settings['ticket_priority'];
                if ($settings['tags'])
                    $data['tags'] = implode(' ', $settings['tags']);
            }

            if (array_key_exists('pull_mode', $data)) {
                return view('telegram.admin_ui_old_users', $data);
            }

            return view('telegram.admin_ui', $data);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->showErrorMessageAdminUI(['Please contact support.'], $data);
        }
    }

    public function showErrorMessageAdminUI($errors, $data)
    {
        $data['errors'] = $errors;
        if(array_key_exists('pull_mode', $data))
        {
            return view('telegram.admin_ui_old_users', $data);
        }
        return view('telegram.admin_ui', $data);
    }

    public function admin_ui_add(Request $request, TelegramService $service)
    {
        try {
            $data = $request->all();
            foreach ($data as $key => $value) {
                if ($value == 'on')
                    $data[$key] = true;
                if ($value == 'off')
                    $data[$key] = false;
            }

            $token = $request->token;
            $return_url = $data['return_url'];
            $subdomain = $data['subdomain'];
            $name = $data['name'];
            $submitURL = $data['submitURL'];
            $has_hello_message = array_key_exists('has_hello_message', $data) ? $data['has_hello_message'] : false;
            $required_user_info = array_key_exists('required_user_info', $data) ? $data['required_user_info'] : false;
            $hello_message = array_key_exists('hello_message', $data) ? $data['hello_message'] : null;

            $data = [
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => $submitURL,
                'token' => $token,
                'has_hello_message' => $has_hello_message,
                'required_user_info' => $required_user_info,
                'hello_message' => $hello_message,
                'ticket_type' => $request->ticket_type,
                'ticket_priority' => $request->ticket_priority,
                'tags' => $request->tags,
                'token_hide' => false
            ];

            if ($request->telegram_mode) {
                $data['pull_mode'] = true;
            }

            if (!$token || !$name) {
                $errors = ['Integration Name and Token is required.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }
            if ($has_hello_message && (!$hello_message || empty($hello_message))) {
                $errors = ['Custom Message is required.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }
            $telegramBot = $service->checkValidTelegramBot($token);
            if (!$telegramBot) {
                $errors = ['Invalid token, use Telegram Bot Father to create one.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }
            if ($service->isTokenRegistered($token)) {
                $errors = ['That telegram token is already registered.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }
            if ($service->isNameRegistered($subdomain, $name)) {
                $errors = ['That integration name is already registered.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }

            if (!$request->telegram_mode) {
                Log::debug("Enabling Telegram Webhook.");
                $telegramResponse = $service->setWebhook($token);
                if (!$telegramResponse || !$telegramResponse[0]) {
                    $errors = ['There was an error with bots configuration, please contact support.'];
                    return $this->showErrorMessageAdminUI($errors, $data);
                }
            }

            $tags = null;
            if ($request->tags && !empty($request->tags)) {
                $tags = json_encode(explode(' ', $request->tags), true);
            }

            $settings = [
                'has_hello_message' => $has_hello_message,
                'required_user_info' => $required_user_info,
                'hello_message' => $hello_message,
                'ticket_type' => $request->ticket_type,
                'ticket_priority' => $request->ticket_priority,
                'tags' => $tags
            ];
            $metadata = $service->registerNewIntegration([
                'name' => $name,
                'token' => $token,
                'subDomain' => $subdomain,
                'settings' => $settings
            ]);
            if (!$metadata) {
                $errors = ['There was an error processing your data, please check your information or contact support.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }
            Log::debug(json_encode($metadata));
            return view('telegram.post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($metadata)]);

        } catch (\Exception $exception) {
            Log::error($exception);
            return 'Please contact support.';
        }
    }

    public function admin_ui_edit(Request $request, TelegramService $service)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            if ($value == 'on')
                $data[$key] = true;
            if ($value == 'off')
                $data[$key] = false;
        }

        $token = $request->token;
        $return_url = $data['return_url'];
        $subdomain = $data['subdomain'];
        $name = $data['name'];
        $submitURL = $data['submitURL'];
        $has_hello_message = array_key_exists('has_hello_message', $data) ? $data['has_hello_message'] : false;
        $required_user_info = array_key_exists('required_user_info', $data) ? $data['required_user_info'] : false;
        $hello_message = array_key_exists('hello_message', $data) ? $data['hello_message'] : null;

        //This code is only to old integrations without settings.
        if($request->has('telegram_mode_without_settings'))
        {
            try{
                $metadata = $service->getMetadataFromSavedIntegrationByToken($request->token);
                return view('telegram.post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($metadata)]);
            }catch (\Exception $exception)
            {
                return 'Please contact support.';
            }
        }

        $data = [
            'return_url' => $return_url,
            'subdomain' => $subdomain,
            'name' => $name,
            'submitURL' => $submitURL,
            'token' => $token,
            'has_hello_message' => $has_hello_message,
            'required_user_info' => $required_user_info,
            'hello_message' => $hello_message,
            'ticket_type' => $request->ticket_type,
            'ticket_priority' => $request->ticket_priority,
            'tags' => $request->tags,
            'token_hide' => true
        ];

        if ($request->telegram_mode) {
            $data['pull_mode'] = true;
        }

        try {
            if (!$token || !$name) {
                $errors = ['Integration Name and Token is required.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }
            if ($has_hello_message && (!$hello_message || empty($hello_message))) {
                $errors = ['Custom Message is required.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }
            $telegramBot = $service->checkValidTelegramBot($token);
            if (!$telegramBot) {
                $errors = ['Invalid token, use Telegram Bot Father to create one.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }

            $telegramResponse = $service->setWebhook($token);
            if (!$telegramResponse || !$telegramResponse[0]) {
                $errors = ['There was an error with bots configuration, please contact support.'];
                return $this->showErrorMessageAdminUI($errors, $data);
            }

            $tags = null;
            if ($request->tags && !empty($request->tags)) {
                $tags = json_encode(explode(' ', $request->tags), true);
            }
            $data['tags'] = $tags;

            $settings = [
                'has_hello_message' => $data['has_hello_message'],
                'required_user_info' => $data['required_user_info'],
                'hello_message' => $data['hello_message'],
                'ticket_type' => $data['ticket_type'],
                'ticket_priority' => $data['ticket_priority'],
                'tags' => $tags
            ];
            $data['settings'] = $settings;
            $metadata = $service->updateIntegrationData($data);

            Log::debug(json_encode($metadata));
            return view('telegram.post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($metadata)]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return 'Please contact support.';
        }
    }

    public function pull(Request $request, ChannelService $service)
    {
        Log::debug($request);
        $updates = $service->getUpdates();
        $response = [
            'external_resources' => $updates,
            'state' => ""
        ];
        Log::debug(json_encode($response));
        return response()->json($response);
    }

    public function channelback(Request $request, ChannelService $service)
    {
        try {
            $external_id = $service->channelBackRequest($request->parent_id, $request->message);

            $response = [
                'external_id' => $external_id
            ];
            return response()->json($response);
        } catch (\Exception $exception) {
            throw new ServiceUnavailableHttpException($exception->getMessage());
        }
    }

    public function clickthrough(Request $request)
    {
        Log::info($request->all());
    }

    public function healthcheck(Request $request)
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
