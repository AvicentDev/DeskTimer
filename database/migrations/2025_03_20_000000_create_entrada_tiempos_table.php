<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entrada_tiempos', function (Blueprint $table) {
            $table->id();
            $table->string('estado')->default('pendiente');
            $table->timestamp('tiempo_inicio');
            $table->timestamp('tiempo_fin')->nullable();
            $table->bigInteger('duracion');
            $table->text('descripcion')->nullable();
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrada_tiempos');
    }
};
