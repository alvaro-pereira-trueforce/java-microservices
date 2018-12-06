<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder\MessageFilter;


use APIServices\LinkedIn\Services\LinkedinService;
use APIServices\Zendesk_Linkedin\MessagesBuilder\MessageBuilder;

/**
 * Class MessageFilter
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder\MessageFilter
 */
abstract class MessageFilter extends MessageBuilder
{
    /**
     * @var $comment
     */
    protected $comment;

    /**
     * MessageFilter constructor.
     * @param $metadata
     * @param LinkedinService $linkedinService
     * @throws \Exception
     */
    public function __construct($metadata, LinkedinService $linkedinService)
    {
        $this->linkedinService = $linkedinService;
        $this->comment = $this->linkedinService->getPostLinkedIn($metadata);

    }
}