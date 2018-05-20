<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Facebook\Repositories\FacebookRepository;
use APIServices\Facebook\Services\FacebookService;
use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use GuzzleHttp\Client;

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

        $updates = $service->getUpdates();

        Log::info($updates);
        return response()->json($updates);
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
        $submitURL = env('APP_URL') . '/instagram/';

        try {
            if (!$metadata) {
                return view('instagram.admin_ui', [
                    'app_id' => env('FACEBOOK_APP_ID'),
                    'return_url' => $return_url,
                    'subdomain' => $subdomain,
                    'name' => $name,
                    'submitURL' => $submitURL
                ]);
            }
            dd($metadata);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException("There is a problem please contact with support.");
        }
    }

    public function admin_create_facebook_registration(Request $request, FacebookRepository $repository) {
        try {
            $newUserToConfirm = $repository->updateOrCreate([
                'zendesk_domain_name' => $request->subdomain,
            ], [
                'integration_name' => $request->name,
                'zendesk_domain_name' => $request->subdomain,
                'status' => false
            ]);
            return response()->json($newUserToConfirm->uuid, 200);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public function admin_wait_facebook(Request $request, FacebookRepository $repository) {
        if ($request->uuid) {
            $newUserToConfirm = $repository->getByUUID($request->uuid);

            if ($newUserToConfirm && $newUserToConfirm->status) {
                return response()->json($newUserToConfirm->uuid, 200);
            }
        }
        return response()->json('', 408);
    }

    public function admin_facebook_auth(Request $request, Client $client, FacebookRepository $repository) {
        Log::debug($request->all());
        try {
            $response = $client->request('GET', 'https://graph.facebook.com/v3.0/oauth/access_token', [
                'query' => [
                    'client_id' => env('FACEBOOK_APP_ID'),
                    'redirect_uri' => env('APP_URL') . '/instagram/admin_facebook_auth',
                    'client_secret' => env('FACEBOOK_APP_SECRET'),
                    'code' => $request->code
                ]
            ]);
            $facebook_data = json_decode($response->getBody()->getContents(), true);
            Log::debug($facebook_data);
            $state = json_decode($request->state, true);
            if (array_key_exists('uuid', $state) && array_key_exists('access_token', $facebook_data)) {
                $facebook_registration = $repository->getByUUID($state['uuid']);
                if ($facebook_registration) {
                    $facebook_registration->fill([
                        'facebook_token' => $facebook_data['access_token'],
                        'status' => true
                    ]);
                    $facebook_registration->save();
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        return view('close_tab_helper');
    }

    public function admin_ui_2(Request $request, FacebookService $service) {
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        $name = $request->name;
        $submitURL = env('APP_URL') . '/instagram/';

        try {
            if (!$request->token) {
                $accessToken = $service->getAccessTokenForNewRegistrationUser($request->uuid);
            } else {
                $accessToken = $request->token;
            }

            $service->setAccessToken($accessToken);
            $pages = $service->getUserPages();

            return view('instagram.admin_ui_valid_user', [
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => $submitURL,
                'accessToken' => $accessToken,
                'pages' => $pages
            ]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('instagram.admin_ui', [
                'app_id' => env('FACEBOOK_APP_ID'),
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => null,
                'submitURL' => $submitURL,
                'errors' => [$exception->getMessage()]
            ]);
        }

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
        $accessToken = $request->token;
        $instagram_id = $request->instagram_id;
        $page_id = $request->page_id;
        $submitURL = env('APP_URL') . '/instagram/';

        if (!$name || !$instagram_id || !$page_id) {
            return view('instagram.admin_ui', [
                'app_id' => env('FACEBOOK_APP_ID'),
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => $submitURL,
                'errors' => ['There was an error processing your request please contact support.']
            ]);
        }

        $metadata = $service->registerNewIntegration($name,
            $accessToken,
            $subdomain,
            $instagram_id,
            $page_id
        );
        return view('instagram.post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => $metadata]);
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
