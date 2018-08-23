<?php

namespace APIServices\Facebook\Controllers;


use APIServices\Facebook\Requests\FacebookGetRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function webhookSubscribe(FacebookGetRequest $request){
        Log::debug("Subscribe:");
        Log::debug($request->all());
        $params = $request->all();
        return $params['hub_challenge'];
    }

    public function webhookHandler(Request $request){
        Log::debug($request->all());
    }
}