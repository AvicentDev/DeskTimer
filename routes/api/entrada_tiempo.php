<?php

use App\Http\Controllers\EntradaTiempoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('entrada_tiempo')
    ->name('entrada_tiempo.')
    ->group(function () {

        // Listar entradas (posibilidad de filtro por rango)
        Route::get('/', [EntradaTiempoController::class, 'index'])->name('index');

        // Crear entrada manual
        Route::post('/', [EntradaTiempoController::class, 'store'])->name('store');

        // Actualizar entrada completa (reemplaza tu antiguo edit/put)
        Route::put('/{id}', [EntradaTiempoController::class, 'edit'])->name('update');

        Route::patch('/{id}', [EntradaTiempoController::class, 'updateDescripcion']);


        // Eliminar entrada
        Route::delete('/{id}', [EntradaTiempoController::class, 'eliminar'])->name('eliminar');

        // Cronómetro: iniciar
        Route::post('iniciar', [EntradaTiempoController::class, 'iniciar'])->name('iniciar');

        // Cronómetro: detener
        Route::patch('detener/{id}', [EntradaTiempoController::class, 'detener'])->name('detener');

        // Listar agrupadas por día
        Route::get('listar', [EntradaTiempoController::class, 'listar'])->name('listar');

        // Crear con tiempos y retorno detallado
        Route::post('crear', [EntradaTiempoController::class, 'crearEntradaConTiempos'])->name('crearConTiempos');

        // Reporte semanal
        Route::post('reportes', [EntradaTiempoController::class, 'reporteSemana'])->name('reporteSemana');

        // Asignar o sincronizar múltiples etiquetas (nuevo método)
        Route::patch('{id}/etiquetas', [EntradaTiempoController::class, 'asignarEtiquetas'])
            ->name('asignarEtiquetas');

            Route::get(
                '{entrada}/etiquetas',
                [EntradaTiempoController::class, 'getEtiquetas']
            );

            Route::delete('{id}/etiquetas', [EntradaTiempoController::class, 'desasignarEtiquetas']);

    });
