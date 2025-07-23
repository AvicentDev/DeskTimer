<?php

use App\Http\Controllers\ProyectoArchivado;
use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('proyectos_archivados')
    ->name('proyecto_archivado.')
    ->group(function ()        
    {

    
        Route::get('/', [ProyectoArchivado::class,'index'])->name('index');

        Route::post('{id}/restaurar', [ProyectoArchivado::class, 'restaurar']);
        Route::delete('{id}', [ProyectoArchivado::class, 'destroy']);
        
    });

?>