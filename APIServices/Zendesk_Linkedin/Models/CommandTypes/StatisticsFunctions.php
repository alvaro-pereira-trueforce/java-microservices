<?php


namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;


use Illuminate\Support\Facades\Log;

/**
 * Class StatisticsFunctions
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
class StatisticsFunctions extends CommandType
{
    /**
     * @return void
     * @throws \Throwable
     */
    function handleCommand()
    {
        try {
            if (array_key_exists('functions', $this->statistics['followStatistics'])) {
                $newArray = $this->getTransformArray($this->statistics['followStatistics']);
                $statisticsFunctions = $this->getStatisticFunctionModel($newArray);
                $zendeskResponseBody = $this->getZendeskModel('The following message respond the Command');
                $zendeskResponse = $this->zendeskUtils->addHtmlMessageToBasicResponse($zendeskResponseBody, view('linkedin.commands.statisticsFunction_viewer', [
                    'informationFunctions' => $statisticsFunctions,
                    'message' => 'Companies\'s Functions'
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
    public function getStatisticFunctionModel($statistics)
    {
        try {
            $newStatisticsFunction = [];
            $statisticsInformation = $this->statisticModel($statistics);
            $StatisticFunctions = $statisticsInformation->getFunctions()->all();
            foreach ($StatisticFunctions['single_function'] as $singleFunction) {
                if ($singleFunction['function'] != 'no fount') {
                    $newStatisticsFunction[] = $newArray = [
                        'function' => $singleFunction['function']
                    ];
                }
            }
            return $newStatisticsFunction;
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}