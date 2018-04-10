<?php

namespace App\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Ramsey\Uuid\Uuid;

class Model extends BaseModel
{
    /**
     *  Setup model event hooks
     */
    protected static function boot() {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4()->toString();
        });
    }

    public function getRouteKeyName() {
        return 'uuid';
    }
}
