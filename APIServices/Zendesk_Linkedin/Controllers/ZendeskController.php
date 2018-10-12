<?php

namespace APIServices\Zendesk_Linkedin\Controllers;

use APIServices\Zendesk_Linkedin\Jobs\ProcessZendeskPullEvent;
use APIServices\Zendesk_Linkedin\Models\LinkedInChannel;
use APIServices\LinkedIn\Services\LinkedinService;
use APIServices\Zendesk\Controllers\CommonZendeskController;
use APIServices\Zendesk_Linkedin\Services\ZendeskChannelService;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JavaScript;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ZendeskController extends CommonZendeskController
{

    protected $channel_name = "LinkedIn Channel";
    /** @var LinkedinService $linkedinService */
    protected $linkedinService;
    /** @var ZendeskChannelService $channelService */
    protected $channelService;

    public function __construct(ManifestRepository $manifestRepository, LinkedinService $linkedinService)
    {
        try {
            $this->linkedinService = $linkedinService;
            parent::__construct($manifestRepository, ZendeskChannelService::class, LinkedInChannel::class);
        } catch (\Exception $exception) {
            Log::error('Zendesk Controller Constructor Error:' . $exception->getMessage() . $exception->getLine());
        }
    }

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
                $front_end_variables['backend_variables']['tags'] = $metadata['settings']['tags'];
                $front_end_variables['backend_variables']['ticket_type'] = $metadata['settings']['ticket_priority'];
                $front_end_variables['backend_variables']['ticket_priority'] = $metadata['settings']['ticket_type'];
                $newAccount = array_merge($newAccount, $metadata);
            }

            $newAccount['account_id'] = $newAccountID;
            $this->saveNewAccountInformation($newAccountID, $newAccount);
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
                $newAccount = $this->getNewAccountInformation($request->state);

                if ($request->has('code')) {
                    $newAccount['linkedin_code'] = $request->code;
                }

                if ($request->has('error')) {
                    $newAccount['linkedin_canceled'] = true;
                }
                $this->saveNewAccountInformation($request->state, $newAccount);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }

        return view('close_tab_helper');
    }

    public function admin_UI_waiting(Request $request, LinkedinService $linkedinService)
    {
        /* Example Request
         *  'account_id' => '43a63903-eb23-480f-ae2a-f9e598cea089',
         *  'name' => 'Integration Name'
         */
        try {
            $newAccount = $this->getNewAccountInformation($request->account_id);

            if (array_key_exists('linkedin_code', $newAccount)) {
                $newAccount['name'] = $request->name;
                $LinkedToken = $this->linkedinService->getAuthorizationToken($newAccount['linkedin_code']);
                Log::debug('Access_token');
                unset($newAccount['linkedin_code']);
                $pagesData = $linkedinService->getCompanies($LinkedToken);
                Log::debug($pagesData);
                if (empty($pagesData) || (array_key_exists('_total', $pagesData) && (int)$pagesData['_total'] == 0) ||
                    !array_key_exists('values', $pagesData)) {
                    return response()->json(['linkedIn_no_companies' => true], 404);
                }
                $newAccount = array_merge($newAccount, $LinkedToken);

                $this->saveNewAccountInformation($request->account_id, $newAccount);

                if (array_key_exists('expires_in', $newAccount)) {
                    $expires = $newAccount['expires_in'];
                }
                $response = [
                    'redirect_url' => env('APP_URL') . '/linkedin/admin_ui_save/' . $request->account_id,
                    'company' => $pagesData['values'],
                    'ticket_types' => $this->ticket_types,
                    'ticket_priorities' => $this->ticket_priorities,
                    'expires' => $expires
                ];
                if (!empty($newAccount['settings'])) {
                    $response['ticket_priority'] = $newAccount['settings']['ticket_priority'];
                    $response['ticket_type'] = $newAccount['settings']['ticket_type'];
                    $response['tags'] = $newAccount['settings']['tags'];
                    $response['selected_company'] = $newAccount['company_id'];
                }
                return response()->json($this->cleanArray($response), 200);

            }
            if (array_key_exists('linkedin_canceled', $newAccount)) {
                return response()->json(['linkedin_canceled' => true], 401);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        return response()->json('Not Registered', 440);
    }


    public function admin_ui_validate_company(Request $request)
    {

        $company_information = $request->company_information;

        try {
            $newAccount = $this->getNewAccountInformation($request->account_id);

            if (empty($newAccount['settings'])) {
                $this->channelService->checkIfLinkedIdIsAlreadyRegistered($company_information['id']);
            }
            $newAccount = array_merge($newAccount, [
                'company_id' => $company_information['id'],
                'settings' => [
                    'ticket_type' => $request->ticket_type,
                    'ticket_priority' => $request->ticket_priority,
                    'tags' => $request->tags
                ]
            ]);
            Log::debug($newAccount);
            unset($newAccount['locale']);
            //return response()->json('Not Registered', 440);
            $newAccountDBModel = $newAccount;
            $newAccountDBModel['uuid'] = $newAccount['account_id'];
            $newAccountDBModel['integration_name'] = $newAccount['name'];
            unset($newAccountDBModel['account_id']);
            unset($newAccountDBModel['name']);
            Log::debug($newAccountDBModel);
            //return response()->json('Not Registered', 440);

            $newAccountDBModel = $this->channelService->registerNewChannelIntegration($newAccountDBModel);
            $newAccount['settings'] = $newAccountDBModel['settings'] ? $newAccountDBModel['settings']->toArray() : [];

            Log::debug('A valid Company');

            $this->saveNewAccountInformation($request->account_id, $newAccount);

            return response()->json([
                'redirect_url' => env('APP_URL') . '/linkedin/admin_ui_save/' . $request->account_id
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
            return view('post_metadata', ['return_url' => $return_url, 'name' => $name, 'metadata' => json_encode($newAccount)]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return view('please_contact_support');
        }
    }

    public function pull(Request $request)
    {
        $metadata = json_decode($request->metadata, true);
        $state = json_decode($request->state, true);
        try {
            $integrationChannel = $this->channelService->getChannelIntegration($metadata);
            if (!empty($integrationChannel)) {
                //ProcessZendeskPullEvent::dispatch($integrationChannel, 1);
                $job = new ProcessZendeskPullEvent($integrationChannel, 1);
                $job->handle($metadata);
            } else {
                throw new \Exception("there is no account");
            }
            return $this->successReturn();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw new UnauthorizedHttpException('We can not process the request, Account does not exits.');
        }
    }

    public function channel_back(Request $request)
    {
        return $this->successReturn();
    }

    public function click_through(Request $request)
    {
        Log::info($request->all());
        return $this->successReturn();
    }

    public function health_check(Request $request)
    {
        return $this->successReturn();
    }

    public function event_callback(Request $request)
    {
        Log::debug($request->all());
        return $this->successReturn();
    }
}
