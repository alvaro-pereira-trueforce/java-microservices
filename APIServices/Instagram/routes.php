<?php

Route::get('/{token}/media/{idMedia}/comments', 'InstagramController@getMediaComments');
Route::get('/{token}/listMedia', 'InstagramController@getAllUserMedia');