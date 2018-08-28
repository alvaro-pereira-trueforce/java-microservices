<?php
namespace App\Database\Traits;

use Ramsey\Uuid\Uuid;

trait UUIDTrait {
    /**
     *  Setup model event hooks
     */
    protected static function boot() {
        parent::boot();
        self::creating(function ($model) {
            if(empty($model->uuid))
                $model->uuid = (string) Uuid::uuid4()->toString();
        });
    }

    public function getRouteKeyName() {
        return 'uuid';
    }
}