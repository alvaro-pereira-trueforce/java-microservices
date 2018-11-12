<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;
use Illuminate\Support\Facades\Log;
/**
 * Class ImageTransform
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
class ImageTransformer extends MessageTransformer
{
    /**
     * return all the Images posts with their corresponding comments already transformed into a zendesk format
     * @param $messages
     * @return array
     * @throws \Throwable
     */
    function getTransformedMessage($messages)
    {
        try {
            $groupCommentsSorted=[];
            $groupComment = [];
            $groupPost = [];
            $thead_id = $this->getExternalIdPost($messages);
            $timeExpirationPost=$this->getExpirationTimePost($messages);
            if (array_key_exists('_total', $messages['updateComments']) && (int)$messages['updateComments']['_total'] !== 0) {
                foreach ($messages['updateComments']['values'] as $message) {
                    $responseComment = $this->getUpdateMediaType($message, $thead_id,$timeExpirationPost);
                    $responseUpdate = $this->getUpdatesImages($messages);
                    if(!empty($responseComment)){
                        $groupComment[$responseComment['created_at']] = $responseComment;
                    }
                    $groupPost[$responseUpdate['created_at']] = $responseUpdate;
                    $groupCommentsSorted=$this->sortedComments($groupComment);
                }
            } else {
                $responseUpdate = $this->getUpdatesImages($messages);
                $groupPost[$responseUpdate['created_at']] = $responseUpdate;
            }
            $response = array_merge($groupCommentsSorted, $groupPost);
            return $response;
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'transformed ImageMessage error');
            return [];
        }
    }

    /**
     * return a Image post transformed into the corresponding zendesk format
     * @param $message
     * @return mixed
     * @throws \Throwable
     */
    public function getUpdatesImages($message)
    {
        try {
            $newUpdate = [
                'external_id' => $this->getExternalIdPost($message),
                'message' => $this->getMessagePost($message),
                'created_at' => $this->getCreateAtPost($message),
                'author' => $this->getAuthorPost($message)
            ];
            $this->media = $this->getUpdateMedia($message);
            return $this->zendeskUtils->addHtmlMessageToBasicResponse($newUpdate,
                view('instagram.multimedia.photo_viewer', [
                    'photoURL' => $this->getMediaImageUrl($this->media),
                    'message' => $this->getMessagePost($message)
                ])->render());
        } catch (\Exception $exception) {
            Log::error($exception);
            return [];
        }
    }
}