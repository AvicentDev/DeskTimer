<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Los campos ya se crean como timestamp en la migración principal
        // No es necesario cambiarlos, PostgreSQL maneja timestamp correctamente
    }

    public function down(): void
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->timestamp('tiempo_inicio')->change();
            $table->timestamp('tiempo_fin')->nullable()->change();
        });
    }
};
