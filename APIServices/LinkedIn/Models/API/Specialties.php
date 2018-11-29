<?php


namespace APIServices\LinkedIn\Models\API;

use Illuminate\Support\Facades\Log;

/**
 * Class Specialities.
 *
 * @method Specialty           getSpecialty()
 */
class Specialties extends BaseObject
{
    public function __construct($data)
    {
        try {
            if (!empty($data['values'])) {
                $temp['specialities'] = implode(',',$data['values']);
                unset($data['values']);

                $data['specialty'] = $temp;
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
            'specialty' => Specialty::class
        ];
    }
}