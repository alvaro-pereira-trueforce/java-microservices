<?php

//Comment
Route::get('/{token}/{idIstagram}/{limit}/listMedia', 'InstagramController@getMedia');
Route::get('/{token}/media/{idMedia}/{limit}/comment', 'InstagramController@getComment');
Route::post('/{token}/media/{idMedia}/comment/{message}', 'InstagramController@postComment');