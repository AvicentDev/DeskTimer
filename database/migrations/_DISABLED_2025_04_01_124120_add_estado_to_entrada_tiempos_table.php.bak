<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToEntradaTiemposTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            // Agrega la columna 'estado' con valor por defecto 'pendiente'
            $table->string('estado')->default('pendiente')->after('id'); // Cambia 'after' según tu necesidad
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}
