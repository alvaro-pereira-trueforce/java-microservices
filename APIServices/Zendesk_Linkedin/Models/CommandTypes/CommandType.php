<?php


namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;

use APIServices\LinkedIn\Services\LinkedinService;
use APIServices\Utilities\StringUtilities;
use APIServices\Zendesk\Services\ZendeskAPI;
use APIServices\Zendesk\Services\ZendeskClient;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\App;

/**
 * Class CommandType
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
abstract class CommandType implements ICommandType
{
    protected $request_data;
    protected $linkedinService;
    protected $zendeskUtils;
    protected $metadata;
    protected $authorData;
    protected $nameCommand;
    protected $comment;
    protected $listProfile = [];

    /**
     * CommandType constructor.
     * @param $request_body
     * @param LinkedinService $linkedinService
     * @param Utility $zendeskUtils
     * @throws \Exception
     */
    public function __construct($request_body, LinkedinService $linkedinService, Utility $zendeskUtils)
    {
        $this->linkedinService = $linkedinService;
        $this->zendeskUtils = $zendeskUtils;
        $this->nameCommand = $request_body['nameCommand'];
        $this->request_data = $request_body['body'];
        $this->metadata = $metadata = json_decode($this->request_data->metadata, true);
        $paramsBody['thread_id'] = $this->request_data['thread_id'];
        $paramsBody['access_token'] = $this->metadata['access_token'];
        $paramsAuthor = $linkedinService->getPostLinkedIn($paramsBody);
        $this->authorData = $paramsAuthor['updateContent']['company'];
        $this->comment = $linkedinService->getCommentsLinkedIn($paramsBody);


    }

    /**
     * @return ZendeskAPI
     * @throws \Exception
     */
    public function getZendeskAPIServiceInstance()
    {
        try {
            $api_client = App::makeWith(ZendeskClient::class, [
                'access_token' => $this->metadata['zendesk_access_token']
            ]);

            return App::makeWith(ZendeskAPI::class, [
                'subDomain' => $this->metadata['subdomain'],
                'client' => $api_client,
                'instance_push_id' => $this->metadata['instance_push_id']
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @return array
     */
    public function getZendeskResponseModel()
    {
        return [
            'external_id' => $this->request_data->thread_id . ':' . StringUtilities::RandomString(),
            'message' => 'The following message respond the Command ' . $this->nameCommand . ' ',
            'thread_id' => $this->request_data->thread_id,
            'created_at' => date('Y-m-d\TH:i:s\Z'),
            'author' => [
                'external_id' => strval($this->authorData['id']),
                'name' => $this->authorData['name']
            ]
        ];
    }

    public function getZendeskDefaultModel($message)
    {
        return [[
            'external_id' => $this->request_data->thread_id . ':' . StringUtilities::RandomString(),
            'message' => $message . ': ' . $this->nameCommand,
            'thread_id' => $this->request_data->thread_id,
            'created_at' => date('Y-m-d\TH:i:s\Z'),
            'author' => [
                'external_id' => strval($this->authorData['id']),
                'name' => $this->authorData['name']
            ]
        ]
        ];

    }

    public function getProfileFormat($profile)
    {
        $newProfileData['firstName'] = $profile['person']['firstName'];
        $newProfileData['lastName'] = $profile['person']['lastName'];
        $newProfileData['headline'] = $profile['person']['headline'];
        $newProfileData['siteStandardProfileRequest'] = $profile['person']['siteStandardProfileRequest']['url'];
        $newProfileData['id'] = $profile['person']['id'];
        return $newProfileData;
    }

    public function getUniqueProfile($listProfiles)
    {
        $unique = collect($listProfiles);
        return $unique->unique('id')->values()->all();

    }

}