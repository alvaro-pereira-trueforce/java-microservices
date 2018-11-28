<?php


namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;


/**
 * Class StatisticsSeniorities
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
class StatisticsSeniorities extends CommandType
{
    /**
     * @return void
     * @throws \Throwable
     */
    function handleCommand()
    {
        try {
            if (array_key_exists('industries', $this->statistics['followStatistics'])) {
                $newArray = $this->getTransformArray($this->statistics['followStatistics']);
                $statisticsSeniority = $this->getStatisticSenioritiesModel($newArray);
                $zendeskResponseBody = $this->getZendeskModel('The following message respond the Command');
                $zendeskResponse = $this->zendeskUtils->addHtmlMessageToBasicResponse($zendeskResponseBody, view('linkedin.commands.statisticsSeniority_viewer', [
                    'informationSeniorities' => $statisticsSeniority,
                    'message' => 'Seniorities of Employees'
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
    public function getStatisticSenioritiesModel($statistics)
    {
        try {
            $newStatisticsFunction = [];
            $statisticsInformation = $this->statisticModel($statistics);
            $StatisticSeniorities = $statisticsInformation->getSeniorities()->all();
            foreach ($StatisticSeniorities['seniority'] as $singleSeniority) {
                if ($singleSeniority['seniority'] != 'no fount') {
                    $newStatisticsFunction[] = $newArray = [
                        'seniority' => $singleSeniority['seniority']];
                }
            }
            return $newStatisticsFunction;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}