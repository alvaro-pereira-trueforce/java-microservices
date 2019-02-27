<?php

namespace APIServices\LinkedIn\Models\API;

/**
 * Class Functions.
 *
 * @method SingleFunction               getSingleFunction()
 *
 */
class Functions extends BaseObject
{
    /**
     * Functions constructor.
     * @param $data
     * @throws \Exception
     */
    public function __construct($data)
    {
        if (!empty($data['values'])) {
            $newData = [];
            foreach ($data['values'] as $data) {
                $newData[] = new SingleFunction($data);
            }
            unset($data['entryKey']);
            unset($data['entryValue']);
            unset($data['values']);
            $data['single_function'] = $newData;
        }
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [];
    }
}