<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->string('descripcion', 255)->change(); // o puedes usar text() si quieres más
        });
    }

    public function down(): void
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->string('descripcion', 50)->change(); // suponiendo que antes era 50
        });
    }
};

