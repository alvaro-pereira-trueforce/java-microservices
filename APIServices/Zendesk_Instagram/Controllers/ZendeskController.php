<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Facebook\Repositories\FacebookRepository;
use APIServices\Facebook\Services\FacebookService;
use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use GuzzleHttp\Client;

class ZendeskController extends Controller
{
    /**
     * @var ManifestRepository
     */
    protected $manifest;

    /**
     * ZendeskController constructor.
     * @param ManifestRepository $repository
     */
    public function __construct(ManifestRepository $repository)
    {
        $this->manifest = $repository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getManifest(Request $request)
    {
        Log::info("Zendesk Request: " . $request);
        return response()->json($this->manifest->getByName('Instagram Channel'));
    }

    /**
     * @param ZendeskChannelService $service
     * @return JsonResponse
     */
    public function pull(ZendeskChannelService $service)
    {
        Log::info("Zendesk Request: Pull");
        $updates = $service->getUpdates();
        Log::debug($updates);
        return response()->json($updates);
    }

    /**
     * @param Request $request
     * @param ZendeskChannelService $service
     * @return JsonResponse
     */
    public function channelback(Request $request, ZendeskChannelService $service)
    {
        Log::info($request);
        try {
            $thread_post_id = explode(':', $request->thread_id);
            $message = $request->message;
            $external_id = $service->sendInstagramMessage($thread_post_id[1], $message);
            $response = [
                'external_id' => $external_id
            ];
            return response()->json($response);
        } catch (\Exception $exception) {
            throw new ServiceUnavailableHttpException($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return View
     */
    public function adminUI(Request $request)
    {
        $name = $request->name; //will be null on empty
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        $return_url = $request->return_url;
        $subdomain = $request->subdomain;
        $submitURL = env('APP_URL') . '/instagram/';
        try {
            return view('instagram.admin_ui', [
                'app_id' => env('FACEBOOK_APP_ID'),
                'return_url' => $return_url,
                'subdomain' => $subdomain,
                'name' => $name,
                'submitURL' => $submitURL
            ]);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException("There is a problem please contact with support.");
        }
    }

    /**
     * @param Request $request
     * @param FacebookRepository $repository
     * @return JsonResponse
     */
    public function admin_create_facebook_registration(Request $request, FacebookRepository $repository)
    {
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

    /**
     * @param Request $request
     * @param FacebookRepository $repository
     * @return JsonResponse
     */
    public function admin_wait_facebook(Request $request, FacebookRepository $repository)
    {
        if ($request->uuid) {
            $newUserToConfirm = $repository->getByUUID($request->uuid);

            if ($newUserToConfirm && $newUserToConfirm->status) {
                return response()->json($newUserToConfirm->uuid, 200);
            }
        }
        return response()->json('', 408);
    }

    /**
     * @param Request $request
     * @param Client $client
     * @param FacebookRepository $repository
     * @return View
     */
    public function admin_facebook_auth(Request $request, Client $client, FacebookRepository $repository)
    {
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

    /**
     * @param Request $request
     * @param FacebookService $service
     * @return View
     */
    public function admin_ui_2(Request $request, FacebookService $service)
    {
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

    /**
     * @param Request $request
     * @param FacebookService $service
     * @return JsonResponse
     */
    public function admin_validate_page(Request $request, FacebookService $service)
    {
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

    /**
     * @param Request $request
     * @param InstagramService $service
     * @return View
     */
    public function admin_ui_submit(Request $request, InstagramService $service)
    {
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
        $state = '{"last_post_date":"' . gmdate('Y-m-d\TH:i:s\Z', Carbon::now()->timestamp) . '"}';
        return view('instagram.post_metadata', [
            'return_url' => $return_url,
            'name' => $name,
            'metadata' => $metadata,
            'state' => $state
        ]);
    }

    /**
     * @param Request $request
     */
    public function clickthrough(Request $request)
    {
        Log::info($request->all());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function healthcheck(Request $request)
    {
        return $this->successReturn();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function event_callback(Request $request)
    {
        Log::debug("Event On Zendesk: \n" . $request . "\n");
        return $this->successReturn();
    }

    /**
     * @return JsonResponse
     */
    public function successReturn()
    {
        return response()->json('ok', 200);
    }
}
