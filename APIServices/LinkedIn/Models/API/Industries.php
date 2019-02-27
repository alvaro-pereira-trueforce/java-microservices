<?php

namespace APIServices\LinkedIn\Models\API;

use Illuminate\Support\Facades\Log;

/**
 * Class Industries.
 *
 * @method string getName()
 *
 */
class Industries extends BaseObject
{
    public function __construct($data)
    {
        try {
            if (!empty($data['values'])) {
                $newData = [];
                foreach ($data['values'] as $data) {
                    $newData[] = new Industry($data);
                }
                unset($data['values']);
                $data['industry'] = $newData;
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
        return [];
    }


}