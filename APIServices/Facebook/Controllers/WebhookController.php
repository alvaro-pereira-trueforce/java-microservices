<?php

namespace APIServices\Facebook\Controllers;


use APIServices\Facebook\Requests\FacebookGetRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class WebhookController extends Controller
{
    public function webhookSubscribe(FacebookGetRequest $request)
    {
        try {
            Log::debug("Subscribe:");
            Log::debug($request->all());
            $params = $request->all();
            if ($params['hub_verify_token'] == env('FACEBOOK_APP_SECRET'))
                return $params['hub_challenge'];
            else
                throw new UnauthorizedHttpException('Not Authorized');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw new UnauthorizedHttpException('Not Authorized');
        }
    }

    public function webhookHandler(Request $request)
    {
        Log::debug($request->all());
    }
}