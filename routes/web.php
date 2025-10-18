<?php

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth/google')->group(function () {
    Route::get('redirect',  [GoogleController::class, 'redirectToGoogle']);
    Route::get('callback',  [GoogleController::class, 'handleGoogleCallback']);
    
    // Endpoint de prueba
    Route::get('test', function () {
        return response()->json([
            'message' => 'Google OAuth test',
            'config' => [
                'client_id' => config('services.google.client_id'),
                'has_secret' => !empty(config('services.google.client_secret')),
                'redirect' => config('services.google.redirect'),
            ]
        ]);
    });
});
