<?php

namespace APIServices\Instagram\Models;


use App\Database\Eloquent\Model;
use App\Database\Traits\UUIDTrait;

class InstagramChannel extends Model {
    protected $table = "instagram_channels";

    use UUIDTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'zendesk_app_id', 'integration_name', 'instagram_id', 'page_id'
    ];
}
