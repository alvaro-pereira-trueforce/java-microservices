<?php

namespace APIServices\LinkedIn\Models\API;

use Illuminate\Support\Facades\Log;

/**
 * Class CountsByMonth.
 *

 */
class CountsByMonth extends BaseObject
{
    public function __construct($data)
    {
        try {
            if (!empty($data['values'])) {
                $newData = [];
                foreach ($data['values'] as $data) {
                    $newData[] = new Count($data);
                }
                $data['count'] = $newData;
                unset($data['date']);
                unset($data['newCount']);
                unset($data['totalCount']);
                unset($data['values']);
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