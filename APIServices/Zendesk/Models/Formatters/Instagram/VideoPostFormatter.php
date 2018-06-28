<?php

namespace APIServices\Zendesk\Models\Formatters\Instagram;


use APIServices\Zendesk\Utility;

class VideoPostFormatter extends PostFormatter {
    /**
     * Receive a post Video to be converted.
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
                view('instagram.multimedia.video_viewer', [
                    'photoURL' => $this->post['thumbnail_url'],
                    'videoURL' => $this->post['media_url'],
                    'message' => $this->getFooterPage()
                ])->render()
            );
        }catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * It is the instagram footer, which will be shown as the name of the Zendesk ticket
     *
     * @return string
     */
    function getFooterPage(){
        $footer_page = parent::getFooterPage();
        if ($footer_page!=null){
            return $footer_page;
        }else{
            $media_type = ucfirst(strtolower($this->post['media_type']));
            return $this->owner['username'] . ' has posted a ' . $media_type ;
        }
    }
}