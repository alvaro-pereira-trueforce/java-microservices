<?php

namespace APIServices\Zendesk_Linkedin\Controllers;

use APIServices\Zendesk\Controllers\CommonZendeskController;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZendeskController extends CommonZendeskController
{
    public function __construct(ManifestRepository $repository)
    {
        parent::__construct($repository);
    }

    public function getManifest(Request $request)
    {
        Log::notice("Zendesk Request: " . $request->method() . ' ' . $request->getPathInfo());
        return response()->json($this->manifest->getByName('Linkedin Channel'));
    }

    public function admin_UI(Request $request)
    {
        Log::debug($request->all());
        return "ok";
    }

    public function pull(Request $request)
    {

    }

    public function channel_back(Request $request)
    {

    }

    public function click_through(Request $request)
    {
        Log::info($request->all());
    }

    public function health_check(Request $request)
    {

    }

    public function event_callback(Request $request)
    {

    }
}
