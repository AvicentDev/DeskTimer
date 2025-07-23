<?php

use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('proyectos')
    ->name('proyecto.')
    ->group(function ()        
    {

        Route::get('/', [ProyectoController::class,'index'])->name('index');

        Route::post('/', [ProyectoController::class,'store'])->name('store');


        Route::put('/{proyecto}', [ProyectoController::class,'update'])->name('update');

        Route::delete('/{proyecto}', [ProyectoController::class,'destroy'])->name('destroy');

        Route::get('/{proyecto_id}/miembros', [ProyectoController::class,'miembrosproyectos'])->name('miembrosproyectos');

        Route::post('/{proyecto}/miembros', [ProyectoController::class, 'agregarMiembros'])->name('agregarMiembros');

        Route::delete('/{proyecto}/miembros/{miembro}', [ProyectoController::class, 'eliminarMiembro'])->name('eliminarMiembro');

        Route::post('{id}/archivar', [ProyectoController::class, 'archivar']);

        
        
    });

?>