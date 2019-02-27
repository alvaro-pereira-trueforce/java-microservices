<?php

namespace APIServices\LinkedIn\Models\API;

/**
 * Class Industry.
 * @method string           getCode()
 * @method string           getName()
 */
class Industry extends BaseObject
{
    /**
     * Industry constructor.
     * @param $data
     */
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