<?php
namespace APIServices\Instagram\Logic;

use MetzWeb\Instagram\Instagram;

class InstagramLogic extends Instagram
{
    public function __construct()
    {

    }

    public function getMediaComments($id, $auth = false)
    {
        return $this->_makeCall('media/' . $id . '/comments', $auth);
    }

    public function getUserMedia($auth = false, $id = 'self', $limit = 0)
    {
        return $this->_makeCall('users/' . $id . '/media/recent', $auth, ($id === 'self'), array('count' => $limit));
    }

    public function postUserMedia($auth = false, $id = 'idMedia',$params, $limit = 0)
    {
        return $this->_makeCall('media/' . $id . '/comments', $auth,$params,'POST');
    }
}