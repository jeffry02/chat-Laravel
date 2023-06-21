<?php

Route::get('/', function () {
    return view('welcome');
});

Route::post('messages', [\App\Http\Controllers\MessageController::class, 'store']);
