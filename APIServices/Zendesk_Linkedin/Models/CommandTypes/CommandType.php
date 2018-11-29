<?php


namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;

use APIServices\LinkedIn\Services\LinkedinService;
use APIServices\Utilities\StringUtilities;
use APIServices\Zendesk\Services\ZendeskAPI;
use APIServices\Zendesk\Services\ZendeskClient;
use APIServices\Zendesk\Utility;
use APIServices\Zendesk_Linkedin\Helpers\LinkedInModelService;
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
    protected $companyInfo;
    protected $linkedInModelService;
    protected $companyInformation;
    protected $statistics;

    /**
     * CommandType constructor.
     * @param $request_body
     * @param LinkedinService $linkedinService
     * @param Utility $zendeskUtils
     * @param LinkedInModelService $linkedInModelService
     * @throws \Exception
     */
    public function __construct($request_body, LinkedinService $linkedinService, Utility $zendeskUtils, LinkedInModelService $linkedInModelService)
    {
        try {
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
            $this->companyInfo = $linkedinService->getCompanyInfo($paramsBody);
            $this->statistics = $linkedinService->getStatistics($paramsBody);
            $this->linkedInModelService = $linkedInModelService;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @return ZendeskAPI
     * @throws \Exception
     */
    public function getZendeskAPIServiceInstance()
    {
        try {
            $api_client = App::makeWith(ZendeskClient::class, [
                'access_token' => $this->metadata['zendesk_access_token']]);

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
     * @param $comment
     * @return array
     * @throws \Exception
     */
    public function getZendeskModel($comment)
    {
        try {
            return [
                'external_id' => $this->request_data->thread_id . ':' . StringUtilities::RandomString(),
                'message' => $comment . ': ' . $this->nameCommand,
                'thread_id' => $this->request_data->thread_id,
                'created_at' => date('Y-m-d\TH:i:s\Z'),
                'author' => [
                    'external_id' => strval($this->authorData['id']),
                    'name' => $this->authorData['name']
                ]
            ];
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $listProfiles
     * @return array
     * @throws \Exception
     */
    public function getUniqueProfile($listProfiles)
    {
        try {
            $unique = collect($listProfiles);
            return $unique->unique('id')->values()->all();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $array
     * @return mixed
     */
    public function getTransformArray($array)
    {
        foreach (array_keys($array) as $key) {
            # Working with references here to avoid copying the value,
            # since you said your data is quite large.
            $value = &$array[$key];
            unset($array[$key]);
            # This is what you actually want to do with your keys:
            #  - remove exclamation marks at the front
            #  - camelCase to snake_case
            $transformedKey = snake_case($key);
            # Work recursively
            if (is_array($value))
                $this->getTransformArray($value);
            # Store with new key
            $array[$transformedKey] = $value;
            # Do not forget to unset references!
            unset($value);
        }
        return $array;
    }

    /**
     * @param array $input
     * @param $key
     * @return int
     */
    public function arraySumValues(array $input, $key)
    {
        $sum = 0;
        array_walk($input, function ($item, $index, $params) {
            if (!empty($item[$params[1]]))
                $params[0] += $item[$params[1]];
        }, array(&$sum, $key));
        return $sum;
    }

    /**
     * @param $statistics
     * @return \APIServices\LinkedIn\Models\API\Statistics
     * @throws \Exception
     */
    public function statisticModel($statistics)
    {
        try {
            return $statisticsInformation = $this->linkedInModelService->getCurrentStatisticsInfo($statistics);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

}