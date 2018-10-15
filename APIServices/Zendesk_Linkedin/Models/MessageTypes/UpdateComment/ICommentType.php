<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes\UpdateComment;


/**
 * Interface ICommentType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes\UpdateComment
 */
interface ICommentType
{
    /**
     * @param $comment
     * @return mixed
     */
    function getTransformedComment($comment);
}