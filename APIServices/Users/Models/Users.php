<?php

namespace APIServices\Users\Models;


use App\Database\Eloquent\Model;

class Users extends Model
{
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
