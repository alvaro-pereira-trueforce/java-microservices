<?php

namespace APIServices\Zendesk\Models\Formatters\Instagram;


use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;

class ImagePostFormatter extends PostFormatter {

    /**
     * Receive a post to be converted.
     *
     * @param $post
     * @param $utility
     */
    public function __construct($owner, $post, Utility $utility)
    {
        parent::__construct($owner, $post, $utility);
    }

    /**
     * @return array
     * @throws \Exception
     */
    function getTransformedMessage()
    {
        try{
            $transformedMessages = parent::getTransformedMessage();
            return $this->utility->addHtmlMessageToBasicResponse($transformedMessages,
                view('instagram.multimedia.photo_viewer', [
                    'photoURL' => $this->post['media_url'],
                    'message' => $this->getFooterPage()
                ])->render()
            );
        }catch (\Exception $exception) {
            throw $exception;
        }
    }
}