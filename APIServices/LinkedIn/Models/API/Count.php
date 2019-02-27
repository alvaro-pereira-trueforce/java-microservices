<?php


namespace APIServices\LinkedIn\Models\API;


use Illuminate\Support\Facades\Log;

/**
 * Class Counts
 *
 */
class Count extends BaseObject
{
    /**
     * Count constructor.
     * @param $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        try {
            $arrayMonth = $this->getHelperArray()->getMonthCodes();
            $month = array_get($arrayMonth, $data['date']['month'],'no fount');
            $data = [
                'month' => $month,
                'year' => $data['date']['year'],
                'newCount' => $data['newCount']
            ];
            parent::__construct($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}