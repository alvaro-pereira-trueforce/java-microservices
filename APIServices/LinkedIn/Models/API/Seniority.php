<?php

namespace APIServices\LinkedIn\Models\API;


/**
 * Class Seniority
 * @package APIServices\LinkedIn\Models\API
 */
class Seniority extends BaseObject
{
    /**
     * Seniority constructor.
     * @param $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        try {
            $arraySeniority = $this->getHelperArray()->getSeniorityCodes();
            $seniority = array_get($arraySeniority, $data['entryKey'],'no fount');
            $data = [
                'seniority' => $seniority,
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