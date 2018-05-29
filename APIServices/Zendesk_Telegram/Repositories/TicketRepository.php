<?php

namespace APIServices\Zendesk_Telegram\Repositories;


use APIServices\Zendesk\Models\TicketIdentifier;
use App\Database\Eloquent\RepositoryUUID;
use Illuminate\Support\Facades\App;

class TicketRepository extends RepositoryUUID {

    /**
     * @return TicketIdentifier
     */
    protected function getModel() {
        return App::make(TicketIdentifier::class);
    }

    /**
     * @param $parent_id
     * @return TicketIdentifier
     */
    public function findByParentID($parent_id) {
        $model = $this->getModel();
        return $model->where('parent_identifier', '=', $parent_id)->first();
    }

}