<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder;

use APIServices\LinkedIn\Services\LinkedinService;
use Illuminate\Support\Facades\Log;

/**
 * Class TransformMessagePullEvent
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder
 */
class TransformMessagePullEvent
{
    /**
     * @var $metadata
     */
    protected $metadata;
    /**
     * @var LinkedinService
     */
    protected $linkedinService;
    /**
     * @var array $ListPosts
     */
    protected $ListPosts;

    /**
     * TransformMessagePullEvent constructor.
     * @param $metadata
     * @param LinkedinService $linkedinService
     * @throws \Exception
     */
    public function __construct($metadata, LinkedinService $linkedinService)
    {
        $this->metadata = $metadata;
        $this->linkedinService = $linkedinService;
        $this->ListPosts = $this->linkedinService->getUpdates($metadata);

    }

    /**
     * @param $limitTracking
     * @return array
     * @throws \Exception
     */
    public function getValidatePosts($limitTracking)
    {
        try {
            $newArrayPost = [];
            if (array_key_exists('values', $this->ListPosts)) {
                foreach ($this->ListPosts['values'] as $post) {
                    $newPost = $this->transformLinkedInTimestamp($post['updateContent']['companyStatusUpdate']['share']['timestamp']);
                    if ($newPost > $limitTracking) {
                        $newArrayPost[] = $post;
                    }
                }
                return $newArrayPost;
            } else {
                Log::debug('there is not media or comments');
                return $newArrayPost;
            }
        } catch (\Exception $exception) {
            throw $exception;

        }
    }

    /**
     * @param $message
     * @return string
     */
    public function transformLinkedInTimestamp($message)
    {
        return gmdate('Y-m-d\TH:i:s\Z', $message / 1000);

    }
}