<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EtiquetaController;

Route::middleware('auth:sanctum')
    ->prefix('etiqueta')
    ->name('etiqueta.')
    ->group(function () {
        // Listar todas las etiquetas
        Route::get('/', [EtiquetaController::class, 'index'])->name('index');
        // Crear nueva etiqueta
        Route::post('/', [EtiquetaController::class, 'store'])->name('store');
        // Mostrar una etiqueta
        Route::get('/{etiqueta}', [EtiquetaController::class, 'show'])->name('show');
        // Actualizar etiqueta
        Route::put('/{etiqueta}', [EtiquetaController::class, 'update'])->name('update');
        // Eliminar etiqueta
        Route::delete('/{etiqueta}', [EtiquetaController::class, 'destroy'])->name('destroy');
        // Entradas de tiempo asociadas (muchos-a-muchos)
        Route::get('/{etiqueta}/entradas', [EtiquetaController::class, 'entradas'])->name('entradas');
    });
