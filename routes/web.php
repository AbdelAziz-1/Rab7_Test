<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-translation', function () {
    return __('messages.points_redeemed', ['points' => 10]);
});

