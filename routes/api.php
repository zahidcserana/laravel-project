<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'Api\AuthController@login');


Route::middleware('auth:api')->group(function () {
    Route::resource('users', 'Api\AuthController')->names([
        'store' => 'users.store'
    ]);
    Route::resource('projects', 'Api\ProjectController');
});
