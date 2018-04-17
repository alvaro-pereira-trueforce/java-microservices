<?php

namespace APIServices\Instagram\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use APIServices\Instagram\Models;
use APIServices\Instagram\Logic\InstagramLogic;

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

    public function getMediaComments($id_media, $token)
    {
        $this->instagram->setAccessToken($token);
        try {
            $user = $this->instagram->getMediaComments($id_media, true);
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getAllUserMedia($token)
    {
        $this->instagram->setAccessToken($token);
        try {
            $media = $this->instagram->getUserMedia(true, 'self');
            return response()->json($media, 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }
}
