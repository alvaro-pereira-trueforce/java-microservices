<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk_Instagram\Model\Test;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZendeskController extends Controller
{
    protected $manifest;

    public function __construct(ManifestRepository $repository) {
        $this->manifest = $repository;
    }

    public function getManifest(Request $request)
    {
        Log::info("Zendesk Request: " . $request);
        return response()->json($this->manifest->getByName('Instagram-Integration'));
    }

    public function pull(Request $request, InstagramService $service) {
        Log::info($request);
        Log::info("antes de empzear");
        $metadata = json_decode($request->metadata, true);
        Log::info($metadata);
        $state = json_decode($request->state, true);

        $updates = $service->getInstagramUpdates($metadata['token']);
        $response = [
            'external_resources' => $updates,
            'state' => ""
        ];
        Log::info($response);
        return response()->json($response);
    }
    public function adminUI(Request $request, InstagramService $service) {
        $name = $request->name; //will be null on empty
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        //$locale = $request->locale;
        $submitURL = env('APP_URL') . '/instagram/admin_ui_2';

        $accounts = $service->getByZendeskAppID($subdomain);
        return view('instagram.admin_ui', [
            'return_url' => $return_url,
            'subdomain' => $subdomain,
            'name' => $name,
            'submitURL' => $submitURL,
            'current_accounts' => $accounts
        ]);
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
            return view('instagram.post_metadata', [
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
