<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;
use Illuminate\Support\Facades\Log;
/**
 * Class CommentTransform
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
class CommentTransform extends MessageTransform
{

    /**
     * return all the Comments posts with their corresponding comments already transformed into a zendesk format
     * @return array
     * @throws \Throwable
     */
    function getTransformedMessage()
    {
        try {
            $groupCommentsSorted=[];
            $groupComment = [];
            $groupPost = [];
            $thead_id = $this->getExternalIdPost($this->messages);
            $timeExpirationPost=$this->getExpirationTimePost($this->messages);
            if (array_key_exists('_total', $this->messages['updateComments']) && (int)$this->messages['updateComments']['_total'] !== 0) {
                foreach ($this->messages['updateComments']['values'] as $message) {
                    $responseUpdate = $this->getUpdateComment($this->messages);
                    $responseComment = $this->getUpdateMediaType($message, $thead_id,$timeExpirationPost);
                    if(!empty($responseComment)){
                        $groupComment[$responseComment['created_at']] = $responseComment;
                    }
                    $groupPost[$responseUpdate['created_at']] = $responseUpdate;
                    $groupCommentsSorted=$this->sortedComments($groupComment);
                }
            } else {
                $responseUpdate = $this->getUpdateComment($this->messages);
                $groupPost[$responseUpdate['created_at']] = $responseUpdate;
            }

            $response = array_merge($groupCommentsSorted, $groupPost);
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
        try {
            return [
                'external_id' => $this->getExternalIdPost($messages),
                'message' => $this->getMessagePost($messages),
                'created_at' => $this->getCreateAtPost($messages),
                'author' => $this->getAuthorPost($messages)
            ];
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'transformed CommentMessage error');
            return [];
        }
    }

}