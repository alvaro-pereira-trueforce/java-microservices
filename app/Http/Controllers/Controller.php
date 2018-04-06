<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
//use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Optimus\Bruno\LaravelController;

class Controller extends LaravelController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
