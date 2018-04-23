<?php

namespace APIServices\Zendesk_Telegram\Controllers;

use APIServices\Telegram\Repositories\ChannelRepository;
use APIServices\Telegram\Services\ChannelService;
use App\Repositories\ManifestRepository;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class ZendeskController extends Controller {

    protected $manifest;

    public function __construct(ManifestRepository $repository) {
        $this->manifest = $repository;
    }

    public function getManifest(Request $request) {
        Log::info("Zendesk Request: " . $request);
        return response()->json($this->manifest->getByName('Telegram Channel'));
    }

    public function adminUI(Request $request, ChannelService $service) {
        $name = $request->name; //will be null on empty
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        //$locale = $request->locale;
        $submitURL = env('APP_URL') . '/telegram/admin_ui_2';

        $accounts = $service->getByZendeskAppID($subdomain);
        return view('telegram.admin_ui', [
            'return_url' => $return_url,
            'subdomain' => $subdomain,
            'name' => $name,
            'submitURL' => $submitURL,
            'current_accounts' => $accounts
        ]);
    }

    public function admin_ui_2(Request $request, ChannelService $service) {
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

        $metadata = $service->registerNewIntegration($name, $token, $subdomain);

        if (!$metadata) {
            $errors = ['There was an error processing your data, please check your information or contact support.'];
            return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                $accounts,
                'errors' => $errors]);
        }

        if (array_key_exists('error', $metadata)) {
            $errors = ['That telegram token is already registered.'];
            return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                $accounts,
                'errors' => $errors]);
        }

        return view('telegram.post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($metadata)]);
    }

    public function pull(Request $request, ChannelService $service) {
        Log::info($request);
        $metadata = json_decode($request->metadata, true);
        $state = json_decode($request->state, true);

        $updates = $service->getTelegramUpdates($metadata['token']);
        $response = [
            'external_resources' => $updates,
            'state' => ""
        ];
        Log::info(json_encode($response));
        return response()->json($response);
    }

    public function channelback(Request $request, ChannelService $service) {
        $metadata = json_decode($request->metadata, true);
        $parent_id = explode(':', $request->parent_id);
        $message = $request->message;


        $external_id = $service->sendTelegramMessage($parent_id[1], $parent_id[0], $metadata['token'], $message);

        $response = [
            'external_id' => $external_id
        ];
        return response()->json($response);
    }

    public function clickthrough(Request $request) {
        Log::info($request->all());
    }

    public function healthcheck(Request $request) {
        return $this->successReturn();
    }

    public function event_callback(Request $request) {
        Log::debug("Event On Zendesk: \n" . $request . "\n");
        return $this->successReturn();
    }

    public function successReturn() {
        return response()->json('ok', 200);
    }

    public function handleSubmitForAdminUI(Request $request, ChannelService $service) {
        try {
            $metadata = $service->getMetadataFromSavedIntegration($request->account['uuid']);
            return view('telegram.post_metadata', [
                'return_url' => $request->return_url,
                'name' => $request->account['integration_name'],
                'metadata' => json_encode($metadata)
            ]);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function handleDeleteForAdminUI($uuid, Request $request, ChannelService $service)
    {
        try
        {
            $result = $service->delete($uuid);
            return $this->adminUI($request, $service);
        }catch (\Exception $exception)
        {
            return response()->json($exception->getMessage(), 404);
        }
    }
}
