<?php

namespace APIServices\Zendesk\Models;

use App\Database\Eloquent\ModelUUID;

class TicketIdentifier extends ModelUUID {

    protected $fillable = ['parent_identifier'];

}
