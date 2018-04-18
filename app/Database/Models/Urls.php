<?php

namespace App\Database\Models;


use App\Database\Eloquent\ModelUUID;

class Urls extends ModelUUID
{
    protected $table = 'urls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['admin_ui', 'pull_url', 'channelback_url', 'clickthrough_url', 'healthcheck_url'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['manifest'];

    /**
     * Get the Manifest that owns the Urls.
     */
    public function manifest()
    {
        return $this->belongsTo(Manifest::class,'manifest_uuid','uuid')->withDefault();
    }
}