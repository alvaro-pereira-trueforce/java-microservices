<?php

namespace APIServices\Instagram\Controllers;

use APIServices\Instagram\Models;
use APIServices\Instagram\Logic\InstagramLogic;
use APIServices\Instagram\Services\InstagramService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstagramController extends Controller
{
    protected $instagram;

    public function __construct(InstagramLogic $instagramLogic)
    {
        $this->instagram = $instagramLogic;
        $this->instagram->setApiKey('c133bd0821124643a3a0b5fbe77ee729');
        $this->instagram->setApiSecret('308973f7f4944f699a223c74ba687979');
        $this->instagram->setApiCallback('https://twitter.com/soysantizeta');
    }

    public function getMediaComments($token,$id_media)
    {
        $this->instagram->setAccessToken($token);
        try {
            $comments = $this->instagram->getMediaComments($id_media, true);
            return response()->json($comments, 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return response()->json($e, 500);
        }
    }

    public function getAllUserMedia($token)
    {
        $this->instagram->setAccessToken($token);
        try {
            $user_media = $this->instagram->getUserMedia(true);
            return response()->json($user_media, 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return response()->json($e, 500);
        }
    }

    public function postMediaComments($token,$id_media)
    {
        $this->instagram->setAccessToken($token);
        try {
            $array = array('text'=>'como estan este es un post');
            $user_media = $this->instagram->postUserMedia(true,$id_media,$array,0);
            return response()->json($user_media, 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return response()->json($e, 500);
        }
    }
}
