<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk\Controllers\CommonZendeskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use JavaScript;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ZendeskController extends CommonZendeskController
{

    protected $channel_name = "Instagram Channel";

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
                //This is the code for new users.
                $newAccountID = $metadata['account_id'];
                $front_end_variables['backend_variables']['metadata'] = true;
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

    public function admin_UI_waiting(Request $request, FacebookService $facebookService)
    {
        /* Example Request
         *  'account_id' => '43a63903-eb23-480f-ae2a-f9e598cea089',
         *  'name' => 'Integration Name'
         */
        try {
            $newAccount = $this->getNewAccountInformation($request->account_id);
            if (array_key_exists('code', $newAccount)) {
                $access_token = $facebookService->getAccessTokenFromFacebookCode($newAccount['code']);
                $facebookService->setAccessToken($access_token['access_token']);
                $pages = $facebookService->getUserPages();


                $newAccount['name'] = $request->name;
                unset($newAccount['code']);
                $newAccount = array_merge($newAccount, $access_token);
                $this->saveNewAccountInformation($request->account_id, $newAccount);
                Log::debug($newAccount);
                return response()->json([
                    'redirect_url' => env('APP_URL') . '/instagram/admin_ui_save/' . $request->account_id,
                    'pages' => $pages
                ], 200);
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

    public function admin_ui_validate_page(Request $request, FacebookService $service)
    {
        $page_information = $request->page_information;
        $newAccount = $this->getNewAccountInformation($request->account_id);

        try {
            $service->setAccessToken($newAccount['access_token']);
            $instagram_id = $service->getInstagramAccountFromUserPage($page_information['id']);
            $pageAccessToken = $service->getPageAccessToken($page_information['id']);
            $service->setAccessToken($pageAccessToken);
            $service->setSubscribePageWebHooks($page_information['id']);

            $newAccount = array_merge($newAccount, [
                'instagram_id' => $instagram_id,
                'page_access_token' => $pageAccessToken
            ]);
            Log::debug("Is A Valid Page:");
            Log::debug($newAccount);
            $this->saveNewAccountInformation($request->account_id, $newAccount);
            return response()->json([
                'redirect_url' => env('APP_URL') . '/instagram/admin_ui_save/' . $request->account_id
            ], 200);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
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

    public function pull(Request $request)
    {
        // TODO: Implement pull() method.
    }

    public function channel_back(Request $request)
    {
        // TODO: Implement channel_back() method.
    }

    public function click_through(Request $request)
    {
        // TODO: Implement click_through() method.
    }

    public function health_check(Request $request)
    {
        // TODO: Implement health_check() method.
    }

    public function event_callback(Request $request)
    {
        // TODO: Implement event_callback() method.
    }
}
