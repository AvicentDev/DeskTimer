<?php

use App\Http\Controllers\ProyectoArchivado;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\TareasArchivadas;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('tareas_archivadas')
    ->name('tareas_archivadas.')
    ->group(function ()        
    {

        Route::get('/', [TareasArchivadas::class, 'index'])
            ->name('index');

        Route::post('/{id}/restaurar', [TareasArchivadas::class, 'restaurar'])
            ->name('restaurar');


        Route::delete('/{tarea_archivada}', [TareasArchivadas::class, 'destroy'])
            ->name('destroy');
        
    });

?>