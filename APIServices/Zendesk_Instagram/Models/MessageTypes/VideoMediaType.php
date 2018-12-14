<?php

namespace APIServices\Zendesk_Instagram\Models\MessageTypes;


use Illuminate\Support\Facades\Log;

class VideoMediaType extends MediaType
{
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
                view('instagram.multimedia.video_viewer', [
                    'photoURL' => $this->media['thumbnail_url'],
                    'videoURL' => $this->media['media_url'],
                    'message' => $this->getFooterPage()
                ])->render()
            );
        } catch (\Exception $exception) {
            Log::error($exception);
            return [];
        }
    }
}