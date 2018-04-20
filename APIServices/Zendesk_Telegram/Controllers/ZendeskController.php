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

    public function admin_ui(Request $request) {
        Log::info($request->all());
    }

    public function admin_ui_2(Request $request) {
        Log::info($request->all());
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

    public function channelback(Request $request) {
        Log::info($request->all());
    }

    public function clickthrough(Request $request) {
        Log::info($request->all());
    }

    public function healthcheck(Request $request) {
        return $this->successReturn();
    }

    public function event_callback(Request $request) {
        Log::debug($request->all());
        return $this->successReturn();
    }

    public function successReturn() {
        return response()->json('ok', 200);
    }
}
