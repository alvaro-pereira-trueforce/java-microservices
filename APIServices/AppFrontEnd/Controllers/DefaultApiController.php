<?php

namespace APIService\AppFrontEnd\Controllers;

use App\Http\Controller as BaseController;
use App\Version;

class DefaultApiController extends BaseController
{
    public function index()
    {
        return response()->json([
            'title'   => 'HelpDesk',
            'version' => Version::getGitTag()
        ]);
    }
}
