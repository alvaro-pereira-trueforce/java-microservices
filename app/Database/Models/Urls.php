<?php

namespace App\Database\Models;


use App\Database\Eloquent\ModelUUID;
use App\Database\Traits\toArrayFiltered;

class Urls extends ModelUUID {
    protected $table = 'urls';

    use toArrayFiltered;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_ui',
        'pull_url',
        'channelback_url',
        'clickthrough_url',
        'healthcheck_url',
        'dashboard_url',
        'about_url',
        'event_callback_url'
    ];

    /**
     * The attributes that are hidden on the serialization
     *
     * @var array
     */
    protected $hidden = ['uuid', 'created_at', 'updated_at', 'manifest_uuid'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['manifest'];

    /**
     * Get the Manifest that owns the Urls.
     */
    public function manifest() {
        return $this->belongsTo(Manifest::class, 'manifest_uuid', 'uuid')->withDefault();
    }

    /**
     * Get the URLS with the domain included
     * @var $value
     * @return string||null
     */
    private function getUrlWithDomain($value)
    {
        if($value)
        {
            return env('APP_URL').'/'.$value;
        }
        else
        {
            return null;
        }
    }

    public function getAdminUiAttribute($value)
    {
        return $this->getUrlWithDomain($value);
    }

    public function getPullUrlAttribute($value)
    {
        return $this->getUrlWithDomain($value);
    }

    public function getChannelbackUrlAttribute($value)
    {
        return $this->getUrlWithDomain($value);
    }

    public function getClickthroughUrlAttribute($value)
    {
        return $this->getUrlWithDomain($value);
    }

    public function getHealthcheckUrlAttribute($value)
    {
        return $this->getUrlWithDomain($value);
    }

    public function getDashboardUrlAttribute($value)
    {
        return $this->getUrlWithDomain($value);
    }

    public function getAboutUrlAttribute($value)
    {
        return $this->getUrlWithDomain($value);
    }

    public function getEventCallbackUrlAttribute($value)
    {
        return $this->getUrlWithDomain($value);
    }
}
