<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Zendesk_Instagram\Model\Test;
use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZendeskController extends Controller
{
//    protected $manifest;
//
//    public function __construct(ManifestRepository $repository) {
//        $this->manifest = $repository;
//    }
//
//    public function getManifest(Request $request)
//    {
//        Log::info("Zendesk Request: " . $request);
//        return response()->json($this->manifest->getByName('InstagramTest'));
//    }

//    public function pull(Request $request, ChannelService $service) {
//        Log::info($request);
//        $metadata = json_decode($request->metadata, true);
//        $state = json_decode($request->state, true);
//
//        $updates = $service->getTelegramUpdates($metadata['token']);
//        $reponse = [
//            'external_resources' => $updates,
//            'state' => $state
//        ];
//        return response()->json($reponse);
//    }
        protected $test_class;

      public function __construct(Test $testClass) {
         $this->test_class = $testClass;
      }

      public function getTestClass(){
          $res = $this->test_class->getTestString();
          return response()->json($res, 200);
      }

}
