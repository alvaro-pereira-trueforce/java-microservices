<?php


namespace APIServices\LinkedIn\Models\API;


/**
 * Class Countries.
 *
 *
 * @method Country           getCountry()
 *
 *
 */
class Countries extends BaseObject
{
    /**
     * Countries constructor.
     * @param $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        if (!empty($data['values'])) {
            $newData = [];
            foreach ($data['values'] as $data) {
                $newData[] = new Country($data);
            }
            unset($data['values']);
            $data['country'] = $newData;
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