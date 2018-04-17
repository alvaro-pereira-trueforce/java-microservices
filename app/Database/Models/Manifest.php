<?php

namespace App\Database\Models;

use App\Database\Eloquent\Concerns\HasFillableRelations;
use App\Database\Eloquent\Model;


class Manifest extends Model
{
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    protected $table = 'manifests';
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'id', 'author', 'version'];

    /**
     * Get the urls record associated with the Manifest.
     */
    public function urls()
    {
        return $this->hasOne(Urls::class,'manifest_uuid','uuid');
    }
}
