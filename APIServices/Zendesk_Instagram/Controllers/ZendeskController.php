<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Facebook\Services\FacebookService;
use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk_Instagram\Models\Services\ZendeskChannelService;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ZendeskController extends Controller {
    protected $manifest;

    public function __construct(ManifestRepository $repository) {
        $this->manifest = $repository;
    }

    public function getManifest(Request $request) {
        Log::info("Zendesk Request: " . $request);
        return response()->json($this->manifest->getByName('Instagram Channel'));
    }

    public function pull(Request $request, ZendeskChannelService $service) {
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

    function getMessages($updates, $state) {
        Log::info("GET MESAGE: " . $state);
        $recent_messages = [];
        foreach ($updates as $data) {
            if ($data["created_at"] >= $state) {
                array_push($recent_messages, $data);
            }
        }
        return $recent_messages;
    }

    public function adminUI(Request $request) {
        $name = $request->name; //will be null on empty
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        $submitURL = env('APP_URL') . '/instagram/admin_ui_2';

        if (!$metadata) {
            return view('instagram.admin_ui', [
                'app_id' => env('FACEBOOK_APP_ID'),
                'graph_version' => env('FACEBOOK_DEFAULT_GRAPH_VERSION'),
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => $submitURL
            ]);
        }
    }

    public function admin_ui_2(Request $request, FacebookService $service) {
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        $name = $request->name;
        $submitURL = $request->submitURL;
        $cookieString = $request->token;

        try {
            $accessToken = $service->getAuthentication($cookieString);
            $pages = $service->getUserPages();
            return view('instagram.admin_ui_valid_user', [
                'app_id' => env('FACEBOOK_APP_ID'),
                'graph_version' => env('FACEBOOK_DEFAULT_GRAPH_VERSION'),
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => env('APP_URL') . '/instagram/',
                'accessToken' => $accessToken,
                'pages' => $pages
            ]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('instagram.admin_ui', [
                'app_id' => env('FACEBOOK_APP_ID'),
                'graph_version' => env('FACEBOOK_DEFAULT_GRAPH_VERSION'),
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => $submitURL,
                'errors' => [$exception->getMessage()]
            ]);
        }
        return view('instagram.post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($metadata)]);
    }


    public function admin_validate_page(Request $request, FacebookService $service) {
        $page_id = $request->page_id;
        $accessToken = $request->access_token;
        $service->setAccessToken($accessToken);
        try {
            return response()->json([
                'instagram_id' => $service->getInstagramAccountFromUserPage($page_id)
            ], 200);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public function admin_ui_submit(Request $request, InstagramService $service) {
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        $name = $request->name;
        $accessToken = $request->access_token;
        $instagram_id = $request->instagram_id;
        $page_id = $request->page_id;

        if (!$name || !$instagram_id || !$page_id) {
            return view('instagram.admin_ui', [
                'app_id' => env('FACEBOOK_APP_ID'),
                'graph_version' => env('FACEBOOK_DEFAULT_GRAPH_VERSION'),
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => env('APP_URL') . '/instagram/admin_ui_2',
                'errors' => ['There is a problem with instagram configuration please try again.']
            ]);
        }

        $metadata = $service->registerNewIntegration($name, $accessToken, $subdomain,
            $instagram_id,
            $page_id);
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

    public function handleDeleteForAdminUI($uuid, Request $request, InstagramService $service) {
        try {
            $result = $service->delete($uuid);
            return $this->adminUI($request, $service);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function channelback(Request $request, InstagramService $service) {
        $metadata = json_decode($request->metadata, true);
        Log::info($request);
        $thread_post_id = explode(':', $request->thread_id);
        $message = $request->message;
        $external_id = $service->sendInstagramMessage($thread_post_id[1], $metadata['token'], $message);
        $response = [
            'external_id' => $external_id
        ];
        return response()->json($response);
    }
}
