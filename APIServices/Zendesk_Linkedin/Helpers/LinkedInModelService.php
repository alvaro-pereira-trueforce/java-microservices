<?php


namespace APIServices\Zendesk_Linkedin\Helpers;

use APIServices\LinkedIn\Models\API\Company;
use APIServices\LinkedIn\Models\API\Statistics;

/**
 * Class LinkedInModelService
 * @package APIServices\Zendesk_Linkedin\Helpers\Company
 */
class LinkedInModelService
{
    /**
     * @param $data
     * @return Company
     * @throws \Exception
     */
    public function getCurrentCompanyInfo($data)
    {
        try {
            return new Company($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $data
     * @return Statistics
     * @throws \Exception
     */
    public function getCurrentStatisticsInfo($data)
    {
        try {
            return new Statistics($data);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}