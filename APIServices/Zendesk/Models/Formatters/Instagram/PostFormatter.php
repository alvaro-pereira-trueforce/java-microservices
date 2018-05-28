<?php

namespace APIServices\Zendesk\Models\Formatters\Instagram;


use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Scalar\String_;

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
            Log::info("DEBUGING .......................................................");
            $post_id = $this->post['id'];
            $created_at = date("c", strtotime($this->post['timestamp']));
            $data = ['priority'=>'higth'];
           // $data = json_encode(['last_post_date' => sprintf('%s', $post_timestamp)])
            return [
                'external_id' => $post_id,
                'message' => $this->getFooterPage(),
                'thread_id' => $this->utility->getExternalID([$this->owner['id'], $post_id]),
                'created_at' => $created_at,
                'author' => [
                    'external_id' => $this->post['username'],
                    'name' => $this->post['username'],
                    'image_url' => $this->owner['profile_picture_url']
                ],
                'display_info' => [[
                    'type'=>'question'
                    ]
                ]
            ];
        } catch (\Exception $exception) {
           throw $exception;
        }
    }

    /**
     * @return String | null
     */
    function getFooterPage(){
        return array_key_exists('caption', $this->post)? $this->post['caption'] : null;
    }
}