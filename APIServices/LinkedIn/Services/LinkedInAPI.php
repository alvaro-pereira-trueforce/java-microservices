<?php

namespace APIServices\LinkedIn\Services;


class LinkedInAPI
{
    protected $client;
    protected $endpoints = [
        'GetAccessToken' => 'https://www.linkedin.com/oauth/v2/accessToken',
        'GetCompanies' => 'https://api.linkedin.com/v1/companies?format=json&is-company-admin=true'

    ];

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

            return $this->client->postFormRequest($this->endpoints['GetAccessToken'], $paramsAccessToken);

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
     * @param array
     * @return array
     * @throws \Exception
     */
    public function getAllUpdatesFromAdministratorProfile($id_company)
    {

        return $this->client->getFormRequest('https://api.linkedin.com/v1/companies/' . $id_company . '/updates?format=json');
    }

    /**
     * @param array
     * @return array
     * @throws \Exception
     */
    public function getUpdatesFromCompany($id_company)
    {

        return $this->client->getFormRequest('https://api.linkedin.com/v1/companies/{id}/updates/key=' . $id_company . '?format=json');
    }

    /**
     * @param array
     * @return array
     * @throws \Exception
     */
    public function getLikesFromCompany($id_company, $id_update)
    {

        return $this->client->getFormRequest('https://api.linkedin.com/v1/companies/' . $id_company . '/updates/key=' . $id_update . '/likes?format=json');
    }

    /**
     * @param array
     * @return array
     * @throws \Exception
     */
    public function postSharesFromCompany($id_company, $code)
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


}