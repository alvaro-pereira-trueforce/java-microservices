<?php

namespace APIServices\LinkedIn\Services;
use GuzzleHttp\Client as HttpClient;

/**
 * Class LinkedInClient
 * @package APIServices\LinkedIn\Services
 */
class LinkedInClient
{
    /** @var HttpClient $httpClient */
    protected $httpClient;

    /**
     * LinkedInClient constructor.
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param $endpoint
     * @param array $body
     * @param array $headers
     * @return array
     * @throws \Exception
     */
    public function postFormRequest($endpoint, array $body, $headers = [])
    {
        try {

            $response = $this->httpClient->request('POST', $endpoint, [
                'form_params' => $body,
                'headers' => $headers
            ]);
            if ($response->getStatusCode() != '200') {
                throw new \Exception(json_decode($response->getBody()->getContents(), true), $response->getStatusCode());
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    /**
     * @param $token_address
     * @param array $token_access
     * @return array
     * @throws \Exception
     */
    public function getFormRequest($token_address, $token_access = [])
    {
        try {
            $response = $this->httpClient->request('GET', $token_address, [
                'headers' => $token_access
            ]);
            if ($response->getStatusCode() != '200') {
                throw new \Exception(json_decode($response->getBody()->getContents(), true), $response->getStatusCode());
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

}