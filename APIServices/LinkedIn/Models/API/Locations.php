<?php

namespace APIServices\LinkedIn\Models\API;

use Illuminate\Support\Facades\Log;

/**
 * Class Locations.
 *
 * @method Address          getAddress()
 */
class Locations extends BaseObject
{
    public function __construct($data)
    {
        try {
            if (!empty($data['values'])) {
                $temp = $data['values'][0]['address'];
                unset($data['values']);
                $data['address'] = $temp;
            }
        } catch (\Exception $exception) {
            Log::debug($exception);
        }

        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'address' => Address::class
        ];
    }
}