<?php


namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;


/**
 * Class StatisticsCounts
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
class StatisticsCounts extends CommandType
{

    /**
     * @return string|void
     * @throws \Throwable
     */
    function handleCommand()
    {
        try {
            if (array_key_exists('countsByMonth', $this->statistics['followStatistics'])) {
                $newArray = $this->getTransformArray($this->statistics['followStatistics']);
                $statisticsCount = $this->getStatisticCountModel($newArray);
                $lasStatisticsCount = array_reverse($statisticsCount);
                $zendeskResponseBody = $this->getZendeskModel('The following message respond the Command');
                $zendeskResponse = $this->zendeskUtils->addHtmlMessageToBasicResponse($zendeskResponseBody, view('linkedin.commands.statisticsCount_viewer', [
                    'informationCounts' => $lasStatisticsCount,
                    'message' => 'Followers per Month'
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
    public function getStatisticCountModel($statistics)
    {
        try {
            $newStatisticsCount = [];
            $statisticsInformation = $this->statisticModel($statistics);
            $StatisticCount = $statisticsInformation->getCountsByMonth()->all();
            foreach ($StatisticCount['count'] as $singleCount) {
                    $newStatisticsCount[] = $newArray = [
                        'month' => $singleCount['month'],
                        'year' => $singleCount['year'],
                        'newCount' => $singleCount['newCount']
                    ];
                }
            return $newStatisticsCount;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}