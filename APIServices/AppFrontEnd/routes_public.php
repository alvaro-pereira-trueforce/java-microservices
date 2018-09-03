<?php

$router->get('/', function () {
    return file_get_contents(public_path().'/app/index.html');
});

$router->get('/access/{key}', function ($key) {
    if(\Illuminate\Support\Facades\Hash::check($key, env('HORIZON_AUTH')))
    {
        session(['horizon_admin' => true]);
        return redirect('horizon');
    }
    else
    {
        session()->forget('horizon_admin');
        return redirect('/');
    }
});