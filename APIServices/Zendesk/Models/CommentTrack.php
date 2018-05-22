<?php

namespace APIServices\Zendesk\Models;

use App\Database\Eloquent\ModelUUID;

class CommentTrack extends ModelUUID {
    protected $table = 'comment_track';

    protected $fillable = [
      'post_id',
      'last_comment_date'
    ];
}