<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleController;  // ✅ Agregado
use Illuminate\Http\Request;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ✅ Rutas de Google OAuth
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Endpoint de prueba
Route::post('/test-register', function (Request $request) {
    return response()->json([
        'message' => 'Test OK',
        'received_data' => $request->all()
    ]);
});





Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

