<?php
namespace APIServices\Instagram\Models;


use App\Database\Eloquent\Model;
use App\Database\Eloquent\ModelUUID;

class InstagramModel extends ModelUUID
{
    protected $table = "instagram";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'api_secret',
    ];
}
