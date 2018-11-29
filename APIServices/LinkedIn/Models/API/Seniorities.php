<?php


namespace APIServices\LinkedIn\Models\API;

/**
 * Class Seniorities.
 *
 * @method Seniority           getSeniority()
 *
 */
class Seniorities extends BaseObject
{

    /**
     * Seniorities constructor.
     * @param $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        if (!empty($data['values'])) {
            $newData = [];
            foreach ($data['values'] as $data) {
                $newData[] = new Seniority($data);
            }
            unset($data['values']);
            $data['seniority'] = $newData;
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