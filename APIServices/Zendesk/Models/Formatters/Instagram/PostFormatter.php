<?php

namespace APIServices\Zendesk\Models\Formatters\Instagram;


use APIServices\Zendesk\Utility;

class PostFormatter extends Formatter {

    protected $post;
    /**
     * Receive a post to be converted.
     *
     * @param $post
     * @param $utility
     */
    public function __construct($post, Utility $utility) {
        $this->post = $post;
    }

    function getTransformedMessage() {

    }
}