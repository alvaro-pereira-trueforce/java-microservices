<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZendeskController extends Controller
{
    protected $manifest;

    public function __construct(ManifestRepository $repository) {
        $this->manifest = $repository;
    }

    public function getManifest(Request $request) {
        Log::info("Zendesk Request: ".$request);
        return response()->json($this->manifest->getByName('InstagramTest'));
    }
}
