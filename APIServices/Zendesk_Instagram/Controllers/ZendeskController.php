<?php

namespace APIServices\Zendesk_Instagram\Controllers;

use APIServices\Zendesk\Controllers\CommonZendeskController;
use Illuminate\Http\Request;

class ZendeskController extends CommonZendeskController {

    protected $channel_name = "Instagram Channel";

    public function admin_UI(Request $request)
    {
        // TODO: Implement admin_UI() method.
    }

    public function pull(Request $request)
    {
        // TODO: Implement pull() method.
    }

    public function channel_back(Request $request)
    {
        // TODO: Implement channel_back() method.
    }

    public function click_through(Request $request)
    {
        // TODO: Implement click_through() method.
    }

    public function health_check(Request $request)
    {
        // TODO: Implement health_check() method.
    }

    public function event_callback(Request $request)
    {
        // TODO: Implement event_callback() method.
    }
}
