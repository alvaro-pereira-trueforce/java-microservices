<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Facebook\Jobs\ProcessInstagramEvent;
use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk\Controllers\CommonZendeskController;
use APIServices\Zendesk_Instagram\Models\InstagramChannel;
use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use JavaScript;
use function Psy\debug;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class ZendeskController extends CommonZendeskController
{

    protected $channel_name = "Instagram Channel";
    /** @var FacebookService $facebookService */
    protected $facebookService;
    /** @var ZendeskChannelService $channelService */
    protected $channelService;

    public function __construct(ManifestRepository $repository, FacebookService $facebookService)
    {
        try {
            parent::__construct($repository);
            $this->facebookService = $facebookService;
            $this->channelService = parent::getChannelService(ZendeskChannelService::class, InstagramChannel::class);
        } catch (\Exception $exception) {
            Log::error('Zendesk Controller Constructor Error:');
            Log::error($exception->getMessage());
        }
    }

    public function admin_UI(Request $request)
    {
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty
        Log::debug($request->all());
        /* Example{
            'name' => NULL,
            'metadata' => NULL,
            'state' => NULL,
            'return_url' => 'https://d3v-assuresoft.zendesk.com/zendesk/channels/integration_service_instances/editor_finalizer',
            'instance_push_id' => '3cd1d2a5-aaf2-41fe-a9f1-519499605854',
            'zendesk_access_token' => '601444c6f97f74d331bdc5fb8843b245f16079511759581582227c5643771588',
            'subdomain' => 'd3v-assuresoft',
            'locale' => 'en-US',
        }*/
        try {
            $front_end_variables = $this->getBasicBackendVariables(env('APP_URL') . '/instagram/admin_ui', $request->name);
            $front_end_variables['backend_variables']['client_ID'] = env('FACEBOOK_APP_ID');

            $newAccount = $request->all();
            unset($newAccount['state']);
            unset($newAccount['metadata']);

            if (!$metadata) {
                //This is the code when the user add an account.
                $newAccountID = Uuid::uuid4();
            } else {
                //This is the code for old users.
                $newAccountID = $metadata['account_id'];
                $front_end_variables['backend_variables']['metadata'] = true;
                $newAccount = array_merge($newAccount, $metadata);
            }

            $newAccount['account_id'] = $newAccountID;
            $this->saveNewAccountInformation($newAccountID, $newAccount);

            $front_end_variables['backend_variables']['account_id'] = $newAccountID;
            JavaScript::put($front_end_variables);
            return view('instagram.admin_ui');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }
    }

    public function admin_UI_login(Request $request)
    {
        /*
              array:5 [▼
              "error" => "access_denied"
              "error_code" => "200"
              "error_description" => "Permissions error"
              "error_reason" => "user_denied"
              "state" => "c8d71968-ee68-4c79-b780-55750e560f96"
            ]
            array:2 [▼
              "code" => "AQA6kkSU6P-XBAGRuwRXoQyJpj8mGKnWMJA3ul8EqACquFFvaE9TFAXXD6QjIFVLi0qj_-gN8QCNzkeqLc-e_-yx0tvL-wh0LYko5OPiaTNtqq8z35oTqzFS3avkgd3u8cJuPAIjnA7WWHL1X24jdHndliyIpLH2 ▶"
              "state" => "c8d71968-ee68-4c79-b780-55750e560f96"
            ]

         */
        try {
            if ($request->has('state')) {
                $newAccount = $this->getNewAccountInformation($request->state);

                if ($request->has('code')) {
                    $newAccount['code'] = $request->code;
                }

                if ($request->has('error')) {
                    $newAccount['facebook_canceled'] = true;
                }

                $this->saveNewAccountInformation($request->state, $newAccount);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }

        return view('close_tab_helper');
    }

    public function admin_UI_waiting(Request $request)
    {
        /* Example Request
         *  'account_id' => '43a63903-eb23-480f-ae2a-f9e598cea089',
         *  'name' => 'Integration Name'
         */
        try {
            $newAccount = $this->getNewAccountInformation($request->account_id);
            if (array_key_exists('expires_in', $newAccount)) {
                unset($newAccount['expires_in']);
            }
            if (array_key_exists('code', $newAccount)) {
                $access_token = $this->facebookService->getAccessTokenFromFacebookCode($newAccount['code']);
                Log::debug("Access Token:");
                Log::debug($access_token);
                $this->facebookService->setAccessToken($access_token['access_token']);
                $pages = $this->facebookService->getUserPages();


                $newAccount['name'] = $request->name;
                unset($newAccount['code']);
                $newAccount = array_merge($newAccount, $access_token);
                $this->saveNewAccountInformation($request->account_id, $newAccount);
                $expires = null;
                if (array_key_exists('expires_in', $newAccount)) {
                    $expires = $newAccount['expires_in'];
                }
                Log::debug($newAccount);
                $response = [
                    'redirect_url' => env('APP_URL') . '/instagram/admin_ui_save/' . $request->account_id,
                    'pages' => $pages,
                    'ticket_types' => $this->ticket_types,
                    'ticket_priorities' => $this->ticket_priorities,
                    'expires' => $expires
                ];

                if (!empty($newAccount['settings'])) {
                    $response['ticket_priority'] = $newAccount['settings']['ticket_priority'];
                    $response['ticket_type'] = $newAccount['settings']['ticket_type'];
                    $response['tags'] = $newAccount['settings']['tags'];
                    $response['selected_page'] = $newAccount['page_id'];
                }
                return response()->json($this->cleanArray($response), 200);
            }
            if (array_key_exists('facebook_canceled', $newAccount)) {
                return response()->json(['facebook_canceled' => true], 401);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['facebook_canceled' => true], 401);
        }
        return response()->json('Not Registered', 440);
    }

    public function admin_ui_validate_page(Request $request)
    {
        $page_information = $request->page_information;
        try {
            $newAccount = $this->getNewAccountInformation($request->account_id);


            $this->facebookService->setAccessToken($newAccount['access_token']);
            $instagram_id = $this->facebookService->getInstagramAccountFromUserPage($page_information['id']);

            if (empty($newAccount['settings'])) {
                $this->channelService->checkIfInstagramIdIsAlreadyRegistered($instagram_id);
            }

            $pageAccessToken = $this->facebookService->getPageAccessToken($page_information['id']);
            $this->facebookService->setAccessToken($pageAccessToken);
            $this->facebookService->setSubscribePageWebHooks($page_information['id']);

            $newAccount = array_merge($newAccount, [
                'instagram_id' => $instagram_id,
                'page_access_token' => $pageAccessToken,
                'page_id' => $page_information['id'],
                'settings' => [
                    'ticket_type' => $request->ticket_type,
                    'ticket_priority' => $request->ticket_priority,
                    'tags' => $request->tags
                ]
            ]);

            $newAccountDBModel = $newAccount;
            $newAccountDBModel['uuid'] = $newAccount['account_id'];
            $newAccountDBModel['integration_name'] = $newAccount['name'];

            $newAccountDBModel = $this->channelService->registerNewChannelIntegration($newAccountDBModel);
            $newAccount['settings'] = $newAccountDBModel['settings'] ? $newAccountDBModel['settings']->toArray() : [];
            Log::debug("Is A Valid Page:");
            Log::debug($newAccount);
            $this->saveNewAccountInformation($request->account_id, $newAccount);

            $this->sendOldPosts($request->old_post_number, $newAccount['instagram_id'], $newAccountDBModel);

            return response()->json([
                'redirect_url' => env('APP_URL') . '/instagram/admin_ui_save/' . $request->account_id
            ], 200);
        } catch (\Exception $exception) {
            Log::error("Controller Error: " . $exception->getMessage() . " Line:" . $exception->getLine());
            return response()->json(['message' => $exception->getMessage(), 'status' => 400], 400);
        }
    }

    public function admin_ui_save($account_id)
    {
        try {
            $newAccount = $this->getNewAccountInformation($account_id);
            $this->deleteNewAccountInformation($account_id);
            $return_url = $newAccount['return_url'];
            $name = $newAccount['name'];
            unset($newAccount['return_url']);
            unset($newAccount['name']);
            $newAccount = $this->cleanArray($newAccount);
            Log::debug($newAccount);
            return view('post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($newAccount, true)]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }
    }

    protected function sendOldPosts($postsAmount, $instagram_id, $instagramChannel)
    {

        try {
            $this->facebookService->setInstagramID($instagram_id);
            $this->facebookService->setAccessToken($instagramChannel['page_access_token']);
            if (!empty($postsAmount)) {
                Log::notice('Getting old posts to send...' . $postsAmount);
                $posts = $this->facebookService->getInstagramMediaWithComments($postsAmount);
                Log::debug($posts);
                if (!empty($posts['data'])) {
                    foreach ($posts['data'] as $post) {
                        if (!empty($post['comments']['data'])) {
                            foreach ($post['comments']['data'] as $comment) {
                                if (!empty($comment['id'])) {
                                    $payload = [
                                        'id' => $comment['id'],
                                        'text' => $comment['text'],
                                        'media' => [
                                            'id' => $post['id']
                                        ]
                                    ];
                                    Log::debug($payload);
                                    ProcessInstagramEvent::dispatch($instagramChannel, 'comments', $payload, 1)->delay(15);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }

    public function pull(Request $request)
    {
        return $this->successReturn();
    }

    public function channel_back(Request $request)
    {
        Log::debug($request);
        try {
            $thread_id = explode(':', $request->thread_id);
            $message = $request->message;
            $newPost = $this->facebookService->postInstagramComment($thread_id[0], $message);
            Log::debug($newPost);
            if (empty($newPost)) {
                /** @var ZendeskChannelService $channelService */
                $channelService = $this->getChannelService(ZendeskChannelService::class, InstagramChannel::class);
                $metadata = json_decode($request->metadata, true);
                $channelService->configureZendeskAPI($metadata['zendesk_access_token'], $metadata['subdomain'], $metadata['instance_push_id']);
                $channelService->sendUpdate([
                        [
                            'external_id' => $request->thread_id . ':' . 'error',
                            'message' => 'The following message couldn\'t be posted on your Instagram Business Account. 
                    Facebook denied the action because of security reasons, please try again later. 
                    In order to not have this issue wait 30 seconds between each message sent from Zendesk.',
                            'thread_id' => $request->thread_id,
                            'created_at' => date('Y-m-d\TH:i:s\Z'),
                            'author' => [
                                'external_id' => $request->recipient_id
                            ]
                        ]
                    ]
                );
                return $this->successReturn();
            }
            $response = [
                'external_id' => $request->thread_id . ':' . $newPost['id']
            ];
            return response()->json($response);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw new ServiceUnavailableHttpException($exception->getMessage());
        }
    }

    public function click_through(Request $request)
    {
        Log::info($request->all());
    }

    public function health_check(Request $request)
    {
        return $this->successReturn();
    }

    public function event_callback(Request $request)
    {
        try {
            Log::notice("Zendesk Event:");
            $this->configureChannelRepository(InstagramChannel::class);
            $event = $this->getEventHandler('instagram_' . $request->events[0]['type_id'], $request->all());
            $event->handleEvent();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage() . ' Line: ' . $exception->getLine());
        }

        return $this->successReturn();
    }
}
