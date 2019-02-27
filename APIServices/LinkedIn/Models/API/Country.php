<?php

namespace APIServices\LinkedIn\Models\API;

/**
 * Class Country
 *
 * @method string           getEntryKey()
 *
 */
class Country extends BaseObject
{
    /**
     * Country constructor.
     * @param $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        try {
            $arrayCountry = $this->getHelperArray()->getGeographyCodes();
            $country = array_get($arrayCountry, $data['entryKey'],'no fount');
            $data = [
                'country' => $country,
            ];
            parent::__construct($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}