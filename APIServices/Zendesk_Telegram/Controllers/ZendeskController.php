<?php

namespace APIServices\Zendesk_Telegram\Controllers;

use APIServices\Telegram\Services\TelegramService;
use APIServices\Zendesk_Telegram\Services\ChannelService;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
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
        Log::debug($request->all());
        $name = $request->name; //will be null on empty
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        //$locale = $request->locale;
        $instance_push_id = $request->instance_push_id;
        $zendesk_access_token = $request->zendesk_access_token;
        $submitURL = env('APP_URL') . '/telegram/admin_ui_2';
        try {

            $newRecord = $service->setAccountRegistration([
                'zendesk_access_token' => $zendesk_access_token,
                'instance_push_id' => $instance_push_id,
                'zendesk_app_id' => $subdomain
            ]);
            if (!$newRecord)
                throw new \Exception('There was an error');


            $accounts = $service->getByZendeskAppID($subdomain);
            return view('telegram.admin_ui', [
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => $submitURL,
                'current_accounts' => $accounts
            ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                [],
                'errors' => ['Please contact support.']]);
        }
    }

    public function admin_ui_2(Request $request, TelegramService $service)
    {
        try {
            $token = $request->token;
            $return_url = $request->return_url;
            $subdomain = $request->subdomain;
            $name = $request->name;
            $submitURL = $request->submitURL;
            $accounts = $service->getByZendeskAppID($subdomain);

            if (!$token || !$name) {
                $errors = ['Both fields are required.'];
                return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                    $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                    $accounts,
                    'errors' => $errors]);
            }
            $telegramBot = $service->checkValidTelegramBot($token);
            if (!$telegramBot) {
                $errors = ['Invalid token, use Telegram Bot Father to create one.'];
                return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                    $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                    $accounts,
                    'errors' => $errors]);
            }
            if ($service->isTokenRegistered($token)) {
                $errors = ['That telegram token is already registered.'];
                return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                    $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                    $accounts,
                    'errors' => $errors]);
            }
            if($service->isNameRegistered($subdomain, $name))
            {
                $errors = ['That integration name is already registered.'];
                return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                    $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                    $accounts,
                    'errors' => $errors]);
            }

            $telegramResponse = $service->setWebhook($token);
            if (!$telegramResponse || !$telegramResponse[0]) {
                $errors = ['There was an error with bots configuration, please contact support.'];
                return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                    $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                    $accounts,
                    'errors' => $errors]);
            }
            if ($telegramResponse[0]) {
                $metadata = $service->registerNewIntegration($name, $token, $subdomain);
                if (!$metadata) {
                    $errors = ['There was an error processing your data, please check your information or contact support.'];
                    return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                        $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                        $accounts,
                        'errors' => $errors]);
                }
                Log::debug(json_encode($metadata));
                return view('telegram.post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($metadata)]);
            }
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
        Log::debug("Event On Zendesk: \n" . $request->getContent() . "\n");
        return $this->successReturn();
    }

    public function successReturn()
    {
        return response()->json('ok', 200);
    }

    public function handleSubmitForAdminUI(Request $request, TelegramService $service)
    {
        try {
            $metadata = $service->getMetadataFromSavedIntegration($request->uuid);
            return view('telegram.post_metadata', [
                'return_url' => $request->return_url,
                'name' => $request->integration_name,
                'metadata' => json_encode($metadata)
            ]);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function handleDeleteForAdminUI($uuid, Request $request, TelegramService $service)
    {
        try {
            $result = $service->delete($uuid);
            $accounts = $service->getByZendeskAppID($request->subdomain);
            return $accounts;
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 404);
        }
    }
}
