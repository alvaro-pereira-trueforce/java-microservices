<?php

namespace APIServices\LinkedIn\Models\API;


/**
 * Class Address.
 *
 *
 * @method string           getCity()
 * @method string           getPostalCode()
 * @method string           getStreet1()
 *
 */
class Address extends BaseObject
{

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}