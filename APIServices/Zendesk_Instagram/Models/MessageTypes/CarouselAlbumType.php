<?php

namespace APIServices\Zendesk_Instagram\Models\MessageTypes;


use Illuminate\Support\Facades\Log;

class CarouselAlbumType extends MediaType
{

    function getFooterPage()
    {
        $caption = parent::getFooterPage();
        return str_replace('Carousel_album', 'An album', $caption);
    }

    function addCarouselChildren($basic_response)
    {
        try {
            if (array_key_exists('children', $this->media) && array_key_exists('data', $this->media['children'])) {
                $response = $this->utility->addHtmlMessageToBasicResponse($basic_response,
                    view('instagram.multimedia.carousel_viewer', [
                        'children' => $this->media['children']['data'],
                        'message' => $this->getFooterPage()
                    ])->render()
                );
                return $response;
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
        return $basic_response;
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
            Log::debug('Carousel Media');
            Log::debug($this->media);
            $response = $this->addCarouselChildren($basic_response);
            return $response;
        } catch (\Exception $exception) {
            Log::error($exception);
            return [];
        }
    }
}