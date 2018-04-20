<?php

namespace APIServices\Zendesk_Telegram\Controllers;

use APIServices\Telegram\Repositories\ChannelRepository;
use APIServices\Telegram\Services\ChannelService;
use App\Repositories\ManifestRepository;
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
        return response()->json($this->manifest->getByName('Telegram_Channel'));
    }

    public function adminUI(Request $request) {
        $name = $request->name; //will be null on empty
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        //$locale = $request->locale;
        $submitURL = env('APP_URL') . '/telegram/admin_ui_2';
        return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' => $subdomain, 'name' => $name, 'submitURL' => $submitURL]);
    }

    public function admin_ui_2(Request $request, ChannelService $service) {
        Log::info($request->all());
        $token = $request->token;
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        $name = $request->name;
        $submitURL = $request->submitURL;

        if (!$token) {
            $errors = ['Token field is required.'];
            return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                $subdomain, 'name' => $name, 'submitURL' => $submitURL,
                'errors' => $errors]);
        }
        $telegramBot = $service->checkValidTelegramBot($token);
        if (!$telegramBot) {
            $errors = ['Invalid token, use Telegram Bot Father to create one.'];
            return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                $subdomain, 'name' => $name, 'submitURL' => $submitURL,
                'errors' => $errors]);
        }
        dd($telegramBot);
    }

    public function pull(Request $request, ChannelService $service) {
        Log::info($request);
        $metadata = json_decode($request->metadata, true);
        $state = json_decode($request->state, true);

        $updates = $service->getTelegramUpdates($metadata['token']);
        $reponse = [
            'external_resources' => $updates,
            'state' => $state
        ];
        return response()->json($reponse);
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
}
