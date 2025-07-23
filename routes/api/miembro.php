<?php

use App\Http\Controllers\MiembroController;
use Illuminate\Support\Facades\Route;

    Route::middleware('auth:sanctum')
        ->prefix('miembros')
        ->name('miembro.')
        ->group(function ()        
        {

            Route::get('/', [MiembroController::class,'index'])->name('index');

            Route::post('/', [MiembroController::class,'store'])->name('store');

            Route::put('/{miembro}', [MiembroController::class,'update'])->name('update');

            Route::delete('/{miembro}', [MiembroController::class,'destroy'])->name('destroy');

            
            
        });

?>