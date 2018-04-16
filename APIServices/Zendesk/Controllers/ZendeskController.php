<?php

namespace APIServices\Zendesk\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ZendeskController extends Controller {
    public function getManifest(Request $request) {
        Log::info($request->all());
    }

    public function admin_ui(Request $request) {
        Log::info($request->all());
    }

    public function admin_ui_2(Request $request) {
        Log::info($request->all());
    }

    public function pull(Request $request) {
        Log::info($request->all());
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
