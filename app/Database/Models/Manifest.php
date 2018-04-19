<?php

namespace App\Database\Models;


use App\Database\Eloquent\ModelUUID;
use App\Database\Traits\toArrayFiltered;

class Manifest extends ModelUUID {
    protected $table = 'manifests';

    use toArrayFiltered;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'id', 'author', 'version', 'push_client_id'];

    /**
     * Get the urls record associated with the Manifest.
     */
    public function urls() {
        return $this->hasOne(Urls::class, 'manifest_uuid', 'uuid');
    }

    // this is a recommended way to declare event handlers
    protected static function boot() {
        parent::boot();

        static::deleting(function ($manifest) {
            $manifest->urls()->delete();
        });
    }
}
