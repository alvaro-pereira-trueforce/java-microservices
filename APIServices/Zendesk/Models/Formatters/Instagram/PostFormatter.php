<?php

namespace APIServices\Zendesk\Models\Formatters\Instagram;


use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;

class PostFormatter extends Formatter {

    protected $owner;
    /**
     * @var post
     */
    protected $post;

    /**
     * @var utility
     */
    protected $utility;
    /**
     * Receive a post to be converted.
     *
     * @param $post
     * @param $utility
     */
    public function __construct($owner, $post, Utility $utility)
    {
        $this->owner = $owner;
        $this->post = $post;
        $this->utility = $utility;
    }

    /**
     * @return array
     * @throws \Exception
     */
    function getTransformedMessage()
    {
        try{
            $post_id = $this->post['id'];
            $created_at = date("c", strtotime($this->post['timestamp']));
            return [
                'external_id' => $post_id,
                'message' => $this->getFooterPage(),
                'thread_id' => $this->utility->getExternalID([$this->owner['id'], $post_id]),
                'created_at' => $created_at,
                'author' => [
                    'external_id' => $this->post['username'],
                    'name' => $this->post['username'],
                    'image_url' => $this->owner['profile_picture_url']
                ]
            ];
        } catch (\Exception $exception) {
           throw $exception;
        }
    }

    /**
     * @return string Footer page  of the post or in case it does not exist,
     * returns the user name plus the type of multimeia that has been posted.
     */
    function getFooterPage(){
        if (array_key_exists('caption', $this->post)){
            return $this->post['caption'];
        }else{
            $media_type = strtolower($this->post['media_type']);
            return $this->owner['username'] . 'has posted a ' . $media_type ;
        }
    }
}