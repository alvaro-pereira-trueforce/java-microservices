<?php

namespace App\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use App\Database\Traits\UUIDTrait;

class ModelUUID extends BaseModel{
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    use UUIDTrait;
}