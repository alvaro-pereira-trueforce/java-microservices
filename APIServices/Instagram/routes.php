<?php
Route::get('/media/{idMedia}/comments/{token}', 'InstagramController@getMediaComments');
Route::get('/listMedia/{token}', 'InstagramController@getAllUserMedia');
//getAllUserMediaAndComments
Route::get('/listMedia/comments/{token}', 'InstagramController@getAllUserMediaAndComments');