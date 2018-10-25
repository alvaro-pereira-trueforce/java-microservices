<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;

use APIServices\Zendesk\Utility;

/**
 * Class ImageTransform
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
class ImageTransform extends MessageTransform
{
    /**
     * return all the Images posts with their corresponding comments already transformed into a zendesk format
     * @return array
     * @throws \Throwable
     */
    function getTransformedMessage()
    {
        try {
            $groupComment = [];
            $groupPost = [];
            $thead_id = $this->getExternalIdPost($this->messages);
            if (array_key_exists('_total', $this->messages['updateComments']) && (int)$this->messages['updateComments']['_total'] !== 0) {
                foreach ($this->messages['updateComments']['values'] as $message) {
                    $responseComment = $this->getUpdateMediaType($message, $thead_id);
                    $responseUpdate = $this->getUpdatesImages($this->messages);
                    $groupComment[$responseComment['created_at']] = $responseComment;
                    $groupPost[$responseUpdate['created_at']] = $responseUpdate;
                }
            } else {
                $responseUpdate = $this->getUpdatesImages($this->messages);
                $groupPost[$responseUpdate['created_at']] = $responseUpdate;
            }
            $response = array_merge($groupComment, $groupPost);
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
                    'message' => $this->getFooterPage($this->media)
                ])->render());
        } catch (\Exception $exception) {
            Log::error($exception);
            return [];
        }
    }
}