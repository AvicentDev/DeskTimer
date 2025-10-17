<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Health check endpoint para Render
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected';
    }
    
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => $dbStatus,
        'app' => config('app.name')
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/validate-token', function (Request $request) {
    return response()->json([
        'valid' => true,
        'user' => $request->user(),
    ]);
});


Route::middleware(['auth:sanctum', 'role:empleado'])->group(function () {
    Route::post('/solicitudes', [SolicitudController::class, 'crear']);
});

Route::middleware(['auth:sanctum', 'role:administrador'])->group(function () {
    Route::get('/solicitudes', [SolicitudController::class, 'listar']);
    Route::put('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar']);
    Route::put('/solicitudes/{id}/rechazar', [SolicitudController::class, 'rechazar']);
});


require __DIR__.'/api/user.php';
require __DIR__.'/api/miembro.php';
require __DIR__.'/api/cliente.php'; 
require __DIR__.'/api/proyecto.php';
require __DIR__.'/api/proyecto_archivado.php';
require __DIR__.'/api/tarea.php';
require __DIR__.'/api/tarea_archivada.php';
require __DIR__.'/api/entrada_tiempo.php';
require __DIR__.'/api/etiqueta.php';

require __DIR__.'/api/auth.php';    