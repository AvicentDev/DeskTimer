<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->dateTime('tiempo_inicio')->change();
            $table->dateTime('tiempo_fin')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->timestamp('tiempo_inicio')->change();
            $table->timestamp('tiempo_fin')->nullable()->change();
        });
    }
};
