<?php

use App\Http\Controllers\TareaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('tareas')
    ->name('tarea.')
    ->group(function ()        
    {

        Route::get('/', [TareaController::class,'index'])->name('index');

        Route::post('/', [TareaController::class,'store'])->name('store');

        Route::put('/{tarea}', [TareaController::class,'update'])->name('update');

        Route::delete('/{tarea}', [TareaController::class,'destroy'])->name('destroy');

        Route::get('proyecto/{proyecto_id}', [TareaController::class,'tareasproyecto'])->name('tareasproyecto');

        Route::post('{id}/archivar', [TareaController::class, 'archivar']);
        
    });

?>