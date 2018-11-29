<?php


namespace APIServices\LinkedIn\Models\API;


/**
 * Class SingleFunction
 * @package APIServices\LinkedIn\Models\API
 */
class SingleFunction extends BaseObject
{
    /**
     * SingleFunction constructor.
     * @param $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        try {
            $ArrayFunction = $this->getHelperArray()->getFunctionsCode();
            $function = array_get($ArrayFunction, $data['entryKey']);
            $data = [
                'function' => $function,
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