<?php

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth/google')->group(function () {
    Route::get('redirect',  [GoogleController::class, 'redirectToGoogle']);
    Route::get('callback',  [GoogleController::class, 'handleGoogleCallback']);
});
