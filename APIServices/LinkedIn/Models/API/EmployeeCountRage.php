<?php


namespace APIServices\LinkedIn\Models\API;
/**
 * Class EmployeeCountRage.
 *
 * @method string           getName()
 *
 */

class EmployeeCountRage extends BaseObject
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