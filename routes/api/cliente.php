<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('clientes')
    ->name('cliente.')
    ->group(function () {

        Route::get('/', [ClienteController::class, 'index'])->name('index');

        Route::post('/', [ClienteController::class, 'store'])->name('store');

        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('update');

        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('destroy');
        
        Route::get('/proyectoscliente/{cliente_id}', [ClienteController::class, 'proyectoscliente'])->name('proyectoscliente');
    });

?>
