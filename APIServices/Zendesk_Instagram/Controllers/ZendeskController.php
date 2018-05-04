<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Instagram\Logic\BubbleSorting;
use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk_Instagram\Models\Services\ZendeskChannelService;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

    public function pull(Request $request, ZendeskChannelService $service)
    {
        Log::info($request);
        $metadata = json_decode($request->metadata, true);
        $state = json_decode($request->state, true);
        if ($state != null) {
            $new_state = $state;
            $updates = $service->getUpdates($metadata['token'], $state['most_recent_item_timestamp']);
        } else {
            $new_state = $service->pullState($metadata['token']);
            $updates = $service->getUpdates($metadata['token'], $new_state['most_recent_item_timestamp']);
        }
        $response = [
            'external_resources' => $updates,
            'state' => json_encode($new_state)
        ];
        Log::info($response);
        return response()->json($response);
    }

    function getMessages($updates, $state)
    {
        Log::info("GET MESAGE: " . $state);
        $recent_messages = [];
        foreach ($updates as $data){
            if ($data["created_at"]>=$state){
                array_push($recent_messages,$data);
            }
        }
        return $recent_messages;
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

    public function admin_ui_2(Request $request, InstagramService $service) {
        $token = $request->token;
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        $name = $request->name;
        $submitURL = $request->submitURL;
        $accounts = $service->getByZendeskAppID($subdomain);

        if (!$token || !$name) {
            $errors = ['Both fields are required.'];
            return view('instagram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                $accounts,
                'errors' => $errors]);
        }
//        $telegramBot = $service->checkValidTelegramBot($token);
//        if (!$telegramBot) {
//            $errors = ['Invalid token, use Telegram Bot Father to create one.'];
//            return view('telegram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
//                $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
//                $accounts,
//                'errors' => $errors]);
//        }

        $metadata = $service->registerNewIntegration($name, $token, $subdomain);

        if (!$metadata) {
            $errors = ['There was an error processing your data, please check your information or contact support.'];
            return view('instagram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                $accounts,
                'errors' => $errors]);
        }

        if (array_key_exists('error', $metadata)) {
            $errors = ['That Instagram token is already registered.'];
            return view('instagram.admin_ui', ['return_url' => $return_url, 'subdomain' =>
                $subdomain, 'name' => $name, 'submitURL' => $submitURL, 'current_accounts' =>
                $accounts,
                'errors' => $errors]);
        }

        return view('instagram.post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($metadata)]);
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

    public function handleSubmitForAdminUI(Request $request, InstagramService $service) {
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

    public function handleDeleteForAdminUI($uuid, Request $request, InstagramService $service)
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

    public function channelback(Request $request, InstagramService $service) {
        $metadata = json_decode($request->metadata, true);
         Log::info($request);
        $parent_id = explode(':', $request->recipient_id);
        $message = $request->message;
        $external_id = $service->sendInstagramMessage($parent_id[0], $metadata['token'], $message);
        $response = [
            'external_id' => $external_id
        ];
        return response()->json($response);
    }
}
