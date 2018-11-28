<?php


namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;

use Illuminate\Support\Facades\Log;

/**
 * Class Statistics
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
class Statistics extends CommandType
{
    /**
     * @return void
     * @throws \Throwable
     */
    function handleCommand()
    {
        Log::notice("this is the command ..." . $this->nameCommand);
        try {
            if (!empty($this->statistics)) {
                $basicUpdatesStatistic = $this->getUpdatesStatistics($this->statistics['statusUpdateStatistics']['viewsByMonth']);
                $StatisticFormat = $this->getTransformArray($this->statistics['followStatistics']);
                $basicStatisticsData = $this->getStatisticModel($StatisticFormat);
                $newStatistics = array_merge($basicStatisticsData, $basicUpdatesStatistic);
                $zendeskResponseBody = $this->getZendeskModel('The following message respond the Command');
                $zendeskResponse = $this->zendeskUtils->addHtmlMessageToBasicResponse($zendeskResponseBody, view('linkedin.commands.basicStatistics_viewer', [
                    'information' => $newStatistics,
                    'message' => 'Company Basic Statistics'
                ])->render());
                $this->getZendeskAPIServiceInstance()->pushNewMessage($zendeskResponse);
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
    public function getStatisticModel($statistics)
    {
        try {
            $statisticsInformation = $this->statisticModel($statistics);
            $response = [
                'employees' => $statisticsInformation->getEmployeeCount(),
                'followers' => $statisticsInformation->getCount(),
                'viewers' => $statisticsInformation->getNonEmployeeCount()
            ];
            return $response;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $statistics
     * @return array
     * @throws \Exception
     */
    public function getUpdatesStatistics($statistics)
    {
        try {
            $newStatistics = $statistics['values'];
            $response = [
                'clicks' => $this->arraySumValues($newStatistics, 'clicks'),
                'comments' => $this->arraySumValues($newStatistics, 'comments'),
                'impressions' => $this->arraySumValues($newStatistics, 'impressions'),
                'likes' => $this->arraySumValues($newStatistics, 'likes'),
                'shares' => $this->arraySumValues($newStatistics, 'shares')
            ];
            return $response;
        } catch (\Exception $exception) {
            throw $exception;

        }
    }
}