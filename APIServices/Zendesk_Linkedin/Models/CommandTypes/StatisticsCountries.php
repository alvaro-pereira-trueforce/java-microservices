<?php


namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;


/**
 * Class StatisticsCountries
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
class StatisticsCountries extends CommandType
{
    /**
     * @return void
     * @throws \Throwable
     */
    function handleCommand()
    {
        try {
            if (array_key_exists('countries', $this->statistics['followStatistics'])) {
                $newArray = $this->getTransformArray($this->statistics['followStatistics']);
                $statisticsCountry = $this->getStatisticCountryModel($newArray);
                $zendeskResponseBody = $this->getZendeskModel('The following message respond the Command');
                $zendeskResponse = $this->zendeskUtils->addHtmlMessageToBasicResponse($zendeskResponseBody, view('linkedin.commands.statisticsCountry_viewer', [
                    'informationCountries' => $statisticsCountry,
                    'message' => 'Followers and Employees Countries'
                ])->render());
                $this->getZendeskAPIServiceInstance()->pushNewMessage($zendeskResponse);
            } else {
                $response = $this->getZendeskModel('There is not records to show yet for this Command');
                $this->getZendeskAPIServiceInstance()->pushNewMessage($response);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $statistics
     * @return array
     * @throws \Exception
     */
    public function getStatisticCountryModel($statistics)
    {
        try {
            $newStatisticsCountry = [];
            $statisticsInformation = $this->statisticModel($statistics);
            $StatisticCount = $statisticsInformation->getCountries()->all();
            foreach ($StatisticCount['country'] as $singleCount) {
                if ($singleCount['country'] != 'no fount') {
                    $newStatisticsCountry[] = $newArray = [
                        'country' => $singleCount['country'],
                    ];
                }
            }
            return $newStatisticsCountry;
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}