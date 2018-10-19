<?php

namespace APIServices\Zendesk_Instagram\Models\MessageTypes;


use APIServices\Zendesk\Utility;

abstract class MediaType extends CommentPayload
{
    /**
     * MediaType constructor.
     * @param $media
     * @param $comment
     * @param Utility $utility
     */
    public function __construct($media, $comment, Utility $utility)
    {
        $this->media = $media;
        $this->comment = $comment;
        $this->utility = $utility;
    }
}