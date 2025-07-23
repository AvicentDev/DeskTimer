<?php

use App\Http\Controllers\ProyectoArchivado;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\TareasArchivadas;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('user')
    ->name('user.')
    ->group(function ()        
    {

        Route::put('{id}/update',[UserController::class,'update']);
        Route::put('{id}/password', [UserController::class, 'changePassword']);
        
    });

?>