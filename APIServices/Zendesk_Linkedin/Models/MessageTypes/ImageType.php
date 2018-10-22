<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;

use Illuminate\Support\Facades\Log;

/**
 * Class ImageType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
class ImageType extends MessageType
{


    /**
     * @param $messages
     * @param $access_token
     * @return array|mixed
     * @throws \Throwable
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
                    $responseUpdate = $this->getUpdatesImages($messages);
                    $groupComment[$responseComment['created_at']] = $responseComment;
                    $groupPost[$responseUpdate['created_at']] = $responseUpdate;
                }
            } else {
                $responseUpdate = $this->getUpdatesImages($messages);
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
     * @param $message
     * @return mixed
     * @throws \Throwable
     */
    public function getUpdatesImages($message)
    {
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
    }
}