<?php

namespace App\Database\Models;


use App\Database\Eloquent\ModelUUID;

class Manifest extends ModelUUID
{
    protected $table = 'manifests';

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
