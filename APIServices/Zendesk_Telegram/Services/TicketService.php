<?php

namespace APIServices\Zendesk_Telegram\Services;


use APIServices\Zendesk\Repositories\TicketRepository;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class TicketService
{

    protected $repository;

    public function __construct(TicketRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $parent_id
     * @param $disable_expiration
     * @return string
     */
    public function getValidParentID($parent_id, $disable_expiration = false)
    {
        $ticket = $this->repository->findByParentID($parent_id);

        if ($ticket) {
            $ticket_date = $ticket->updated_at;
            if (!$disable_expiration && $ticket_date->diffInMinutes(Carbon::now()) >= (int)env('TIME_EXPIRE_FOR_TICKETS_IN_MINUTES_TELEGRAM')) {
                $ticket->uuid = (string)Uuid::uuid4()->toString();
                $ticket->save();
            }
            return $ticket->uuid;
        }
        $ticket = $this->repository->create([
            'parent_identifier' => $parent_id
        ]);

        return $ticket->uuid;
    }

    /**
     * @param string $parent_id
     * @return string $chat_id
     */
    public function getExternalParentIDFromParentID($parent_id)
    {
        $ticket = $this->repository->getByUUID($parent_id);
        if ($ticket) {
            return $ticket->parent_identifier;
        }

        return '';
    }

    /**
     * @param $parent_id
     * @return int
     * @throws \Exception
     */
    public function deleteByParentIdentifier($parent_id)
    {
        $ticket = $this->repository->findByParentID($parent_id);
        if (!empty($ticket)) {
            return $ticket->delete();
        }
        return 0;
    }
}