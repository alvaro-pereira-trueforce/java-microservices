<?php

namespace APIServices\Zendesk\Controllers;


use Illuminate\Http\Request;

interface IZendeskController
{
    public function getManifest(Request $request);

    public function admin_UI(Request $request);

    public function pull(Request $request);

    public function channel_back(Request $request);

    public function click_through(Request $request);

    public function health_check(Request $request);

    public function event_callback(Request $request);
}