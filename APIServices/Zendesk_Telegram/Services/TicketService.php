<?php

namespace APIServices\Zendesk_Telegram\Services;


use APIServices\Zendesk_Telegram\Repositories\TicketRepository;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class TicketService {

    protected $repository;

    public function __construct(TicketRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param $parent_id
     * @return string
     */
    public function getValidParentID($parent_id) {
        $ticket = $this->repository->findByParentID($parent_id);

        if ($ticket) {
            $ticket_date = $ticket->updated_at;
            if ($ticket_date->diffInMinutes(Carbon::now()) >= (int) env('TIME_EXPIRE_FOR_TICKETS_IN_MINUTES_TELEGRAM')) {
                $ticket->uuid = (string) Uuid::uuid4()->toString();
                $ticket->save();
            }
            return $ticket->uuid;
        }
        $ticket = $this->repository->create([
            'parent_identifier' => $parent_id
        ]);

        return $ticket->uuid;
    }
}