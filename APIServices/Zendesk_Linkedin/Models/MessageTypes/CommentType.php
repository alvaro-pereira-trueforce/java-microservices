<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;

/**
 * Class CommentType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
class CommentType extends MessageType
{
    /**
     * return all the Comments posts with their corresponding comments already transformed into a zendesk format
     * @param $messages
     * @param $access_token
     * @return array|mixed
     */
    function getTransformedMessage($messages, $access_token)
    {
        try {
            $groupComment = [];
            $groupPost = [];
            $thead_id = $this->getExternalIdPost($messages);
            if (array_key_exists('_total', $messages['updateComments']) && (int)$messages['updateComments']['_total'] !== 0) {
                foreach ($messages['updateComments']['values'] as $message) {
                    $responseComment = $this->getUpdateMediaType($message, $thead_id);
                    $responseUpdate = $this->getUpdateComment($messages);
                    $groupComment[$responseComment['created_at']] = $responseComment;
                    $groupPost[$responseUpdate['created_at']] = $responseUpdate;
                }
            } else {
                $responseUpdate = $this->getUpdateComment($messages);
                $groupPost[$responseUpdate['created_at']] = $responseUpdate;
            }
            $response = array_merge($groupComment, $groupPost);
            return $response;
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'transformed CommentMessage error');
            return [];
        }
    }

    /**
     * return a Comment post transformed into the corresponding zendesk format
     * @param $messages
     * @return array
     */
    public function getUpdateComment($messages)
    {
        return [
            'external_id' => $this->getExternalIdPost($messages),
            'message' => $this->getMessagePost($messages),
            'created_at' => $this->getCreateAtPost($messages),
            'author' => $this->getAuthorPost($messages)
        ];
    }

}