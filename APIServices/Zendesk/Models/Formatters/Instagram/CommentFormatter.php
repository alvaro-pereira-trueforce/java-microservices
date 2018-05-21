<?php

namespace APIServices\Zendesk\Models\Formatters\Instagram;


use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;

class CommentFormatter extends Formatter {
    /**
     * @var thread_id
     */
    protected $thread_id;
    /**
     * @var comment
     */
    protected $comment;
    /**
     * @var Utility
     */
    protected $utility;

    /**
     * CommentFormatter constructor.
     * @param $thread_id
     * @param $comment
     * @param Utility $utility
     */
    public function __construct($thread_id, $comment, Utility $utility)
    {
        $this->thread_id = $thread_id;
        $this->comment = $comment;
        $this->utility = $utility;
    }

    /**
     * @return array
     * @throws \Exception
     */
    function getTransformedMessage()
    {
        try{
            $created_at = date("c", strtotime($this->comment['timestamp']));
            return [
                'external_id' => $this->comment['id'],
                'message' => $this->comment['text'],
                'thread_id' => $this->utility->getExternalID($this->thread_id),
                'created_at' => $created_at,
                'author' => [
                    'external_id' => $this->comment['username'],
                    'name' => $this->comment['username']
                ]
            ];
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}