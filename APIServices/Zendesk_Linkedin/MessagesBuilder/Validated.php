<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder;

use Illuminate\Support\Facades\Log;

/**
 * Class Validated
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder
 */
class Validated extends MessageBuilder
{
    /**
     * @param $limitTracking
     * @return array
     * @throws \Exception
     */
    function getTransformedMessage($limitTracking)
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
}