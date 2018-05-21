<?php

namespace APIServices\Instagram\Controllers;

use APIServices\Facebook\Services\FacebookService;
use APIServices\Instagram\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstagramController extends Controller
{
    protected $facebookService;

    public function __construct(FacebookService $facebookService)
    {
        $this->facebookService =$facebookService;
    }

    public function getMedia($token,$instagram_id,$limit)
    {
        try {
            $user_media = $this->facebookService->getInstagramMedia($token,$instagram_id,$limit);
            return response()->json($user_media, 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return response()->json($e, 500);
        }
    }

    public function getComment($token,$media_id,$limit)
    {
        try {
            $user_comment = $this->facebookService->getInstagramComment($token,$media_id,$limit);
            return response()->json($user_comment, 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return response()->json($e, 500);
        }
    }

    public function postComment($token,$media_id,$message)
    {
        try {
            $user_comment_id = $this->facebookService->postInstagramComment($token,$media_id,$message);
            return response()->json($user_comment_id, 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return response()->json($e, 500);
        }
    }
}
