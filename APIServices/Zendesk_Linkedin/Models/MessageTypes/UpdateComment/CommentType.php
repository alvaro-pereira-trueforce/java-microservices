<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes\UpdateComment;

use APIServices\Zendesk\Utility;

/**
 * Class CommentType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes\UpdateComment
 */
abstract class CommentType implements ICommentType
{
    /**
     * @var Utility
     */
    protected $zendeskUtils;

    /**
     * MessageType constructor.
     * @param Utility $zendeskUtils
     */
    public function __construct(Utility $zendeskUtils)
    {
        $this->zendeskUtils = $zendeskUtils;
    }

    public function getParentId($comment)
    {
        return [
          'thread_type'=>'thread_id',
            'thread_id'=>$comment
        ];
    }
    public function getExternalIdComment(){

    }
    public function getMessageComment(){

    }
    public function getDateComment(){

    }
    public function getAuthor(){

    }
    public function getAuthorInformation(){

    }

}