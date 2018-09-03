<?php

namespace APIServices\Zendesk_Linkedin\Controllers;

use APIServices\Zendesk\Controllers\CommonZendeskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use JavaScript;
use Ramsey\Uuid\Uuid;

class ZendeskController extends CommonZendeskController
{
    protected $channel_name = "Linkedin Channel";
    public function admin_UI(Request $request)
    {
        $metadata = json_decode($request->metadata, true); //will be null on empty
        $state = json_decode($request->state, true); //will be null on empty

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
            $front_end_variables = [
                'backend_variables' => [
                    'account_id' => null,
                    'linkedin_return_URL' => env('APP_URL') . '/linkedin/admin_ui',
                    'client_ID' => env('LINKEDIN_CLIENT_ID'),
                    'name' => $request->name,
                    'metadata' => null
                ]
            ];

            $newAccount = $request->all();
            unset($newAccount['state']);
            unset($newAccount['metadata']);
            Log::debug($request->all());
            if (!$metadata) {
                //This is the code when the user add an account.
                $newAccountID = Uuid::uuid4();
            } else {
                $newAccountID = $metadata['account_id'];
                $front_end_variables['backend_variables']['metadata'] = true;
            }

            $newAccount['account_id'] = $newAccountID;
            Redis::set($newAccountID, json_encode($newAccount, true));
            $front_end_variables['backend_variables']['account_id'] = $newAccountID;
            JavaScript::put($front_end_variables);
            return view('linkedin.admin_ui');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }
    }

    public function admin_UI_login(Request $request)
    {
        /* When the user cancel the request
         * array:3 [▼
          "state" => "987654321"
          "error" => "user_cancelled_authorize"
          "error_description" => "The user cancelled the authorization"
        ]*/

        /* when the user accept
         * array:2 [▼
              "state" => "987654321"
              "code" => "AQS81jrtPtyt8WE1lgUYmQiGVP8QApFYwZKJkpI-bNzx-FXqCfR1BJkLtWR3VlArCaA_KaJT77mFFrHlF6MyIS-oVFV8Q6SfQupJ_P_lrMJbWl5EIsCiD0ri-CvmlFx61ADATWLHHbAFW1XuEGgVvQh0VVg8wt29 ▶"
            ]
         */
        try {
            if ($request->has('state')) {
                $newAccount = json_decode(Redis::get($request->state), true);

                if ($request->has('code')) {
                    $newAccount['linkedin_code'] = $request->code;
                }

                if ($request->has('error')) {
                    $newAccount['linkedin_canceled'] = true;
                }
                Redis::set($request->state, json_encode($newAccount, true));
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
            $newAccount = json_decode(Redis::get($request->account_id), true);
            if (array_key_exists('linkedin_code', $newAccount)) {
                $newAccount['name'] = $request->name;
                return response()->json(['linkedin_invalid_code' => true], 403);
                Redis::set($request->account_id, json_encode($newAccount, true));
                return response()->json([
                    'redirect_url' => env('APP_URL') . '/linkedin/admin_ui_save/' . $request->account_id
                ], 200);
            }
            if (array_key_exists('linkedin_canceled', $newAccount)) {
                return response()->json(['linkedin_canceled' => true], 401);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        return response()->json('Not Registered', 440);
    }

    public function admin_ui_save($account_id)
    {
        try {
            $newAccount = json_decode(Redis::get($account_id), true);
            Redis::del($account_id);
            $return_url = $newAccount['return_url'];
            $name = $newAccount['name'];
            unset($newAccount['return_url']);
            unset($newAccount['name']);
            $newAccount = $this->cleanArray($newAccount);
            Log::debug($newAccount);
            return view('post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($newAccount)]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }
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
