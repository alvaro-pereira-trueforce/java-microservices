<?php


namespace APIServices\LinkedIn\Models\API;

/**
 * Class Speciality.
 *
 * @method string           getCode()
 * @method string           getName()
 *
 */

class Specialty extends BaseObject
{
    public function __construct($data)
    {
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