<?php

namespace APIServices\Zendesk_Instagram\Models\WebHooks;


use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;

class ImageMediaType extends CommentPayload
{
    /**
     * ImageMediaType constructor.
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

    function getFooterPage()
    {
        if (array_key_exists('caption', $this->media)) {
            return $this->media['caption'];
        }
        return $this->comment['username'] . ' has posted an Image';
    }

    function getTransformedMessage()
    {
        try {
            $created_at_media = date("c", strtotime($this->media['timestamp']));

            $basic_response = $this->utility->getBasicResponse(
                $this->getMediaExternalID(),
                $this->getFooterPage(),
                'thread_id',
                $this->getParentID(),
                $created_at_media,
                $this->getMediaAuthorExternalID(),
                $this->getMediaAuthorName()
            );
            return $this->utility->addHtmlMessageToBasicResponse($basic_response,
                view('instagram.multimedia.photo_viewer', [
                    'photoURL' => $this->media['media_url'],
                    'message' => $this->getFooterPage()
                ])->render()
            );
        } catch (\Exception $exception) {
            Log::error($exception);
            return [];
        }
    }
}