<?php

namespace APIServices\LinkedIn\Services;


/**
 * Class LinkedInAPI
 * @package APIServices\LinkedIn\Services
 */
class LinkedInAPI
{
    /**
     * @var LinkedInClient
     */
    protected $client;
    /**
     * @var array
     */
    protected $endpoints = [
        'GetAccessToken' => 'https://www.linkedin.com/oauth/v2/accessToken',
        'GetCompanies' => 'https://api.linkedin.com/v1/companies?format=json&is-company-admin=true'

    ];

    /**
     * LinkedInAPI constructor.
     * @param LinkedInClient $client
     */
    public function __construct(LinkedInClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $code
     * @return array
     *
     * 'grant_type'    The value of this field should always be:  authorization_code Yes
     * 'code'    The authorization code you received from Step 2. Yes
     * 'redirect_uri'    The same 'redirect_uri' value that you passed in the previous step. Yes
     * 'client_id'    The "API Key" value generated Step 1. Yes
     * 'client_secret' The "Secret Key" value generated in Step 1. Yes
     *
     * @throws \Exception
     */
    public function getAuthorizationToken($code)
    {
        try {
            $return_url_token = env('APP_URL') . '/linkedin/admin_ui';
            $paramsAccessToken = [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $return_url_token,
                'client_id' => env('LINKEDIN_CLIENT_ID'),
                'client_secret' => env('LINKEDIN_SECRET'),
            ];

            return $this->client->postAuthorizedRequest($this->endpoints['GetAccessToken'], $paramsAccessToken);

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param array
     * @return array
     * @throws \Exception
     */
    public function getCompanies($params_token)
    {
        $paramsCompanyToken = [
            'Authorization' => 'Bearer ' . $params_token['access_token']
        ];
        return $this->client->getFormRequest($this->endpoints['GetCompanies'], $paramsCompanyToken);
    }

    /**
     * @param array
     * @return array
     * @throws \Exception
     */
    public function getCompanyProfileFromToke($id_company)
    {

        return $this->client->getFormRequest('https://api.linkedin.com/v1/companies/' . $id_company . '?format=json');
    }

    /**
     * @param $params_token
     * @return array
     * @throws \Exception
     */
    public function getAllUpdates($params_token)
    {
        $paramsCompanyToken = [
            'Authorization' => 'Bearer ' . $params_token['access_token']
        ];

        return $this->client->getFormRequest('https://api.linkedin.com/v1/companies/' . $params_token['company_id'] . '/updates?format=json', $paramsCompanyToken);
    }

    /**
     * @param $params_token
     * @return array
     * @throws \Exception
     */
    public function getAllFromPost($params_token)
    {
        $paramsCompanyToken = [
            'Authorization' => 'Bearer ' . $params_token['access_token']
        ];

        return $this->client->getFormRequest('https://api.linkedin.com/v1/companies/' . $params_token['idCompany'] . '/updates/key=' . $params_token['updateKey'] . '?format=json', $paramsCompanyToken);
    }

    /**
     * @param $id_company
     * @param $code
     * @return array
     * @throws \Exception
     */
    public function postSharesCompany($id_company, $code)
    {

        try {
            $return_url_token = env('APP_URL') . '/linkedin/admin_ui';
            $paramsAccessToken = [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $return_url_token,
                'client_id' => env('LINKEDIN_CLIENT_ID'),
                'client_secret' => env('LINKEDIN_SECRET'),
            ];

            return $this->client->postFormRequest('https://api.linkedin.com/v1/companies/' . $id_company . '/shares?format=json', $paramsAccessToken);

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $request_body
     * @return array
     * @throws \Exception
     */
    public function postCommentsCompany($request_body)
    {
        try {
            $paramsRequest = [
                'Authorization' => 'Bearer ' . $request_body['access_token'],
                'Content-Type' => 'application/json',
                'x-li-format' => 'json'
            ];
            $bodyPost=[
                'comment'=> $request_body['linkedinMessage']
            ];
            return $this->client->postFormRequest('https://api.linkedin.com/v1/companies/' . $request_body['company_id'] . '/updates/key=' . $request_body['update_Key'] . '/update-comments-as-company/', $bodyPost, $paramsRequest);

        } catch (\Exception $exception) {
            throw $exception;
        }

    }
    /**
     * @param $request_body
     * @return array
     * @throws \Exception
     */
    public function getLikesLinkedInPost($request_body){
        try {
            $paramsRequest = [
                'Authorization' => 'Bearer ' . $request_body['access_token'],
                'Content-Type' => 'application/json',
            ];
            return $this->client->getFormRequest('https://api.linkedin.com/v1/companies/'.$request_body['company_id'].'/updates/key='.$request_body['update_Key'].'/likes?format=json',$paramsRequest);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
    /**
     * @param $request_body
     * @return array
     * @throws \Exception
     */
    public function getFollowersLinkedInCompany($request_body){
        try {
            $paramsRequest = [
                'Authorization' => 'Bearer ' . $request_body['access_token'],
                'Content-Type' => 'application/json',
            ];
            return $this->client->getFormRequest('https://api.linkedin.com/v1/companies/'.$request_body['company_id'].'/num-followers?format=json',$paramsRequest);
        } catch (\Exception $exception) {
            throw $exception;
        }


    }


}