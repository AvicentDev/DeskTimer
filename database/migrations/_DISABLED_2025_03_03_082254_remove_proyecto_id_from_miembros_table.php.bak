<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('miembros', function (Blueprint $table) {
            // Elimina la restricción de clave foránea
            $table->dropForeign(['proyecto_id']);
            // Ahora elimina la columna
            $table->dropColumn('proyecto_id');
        });
    }
    
    public function down()
    {
        Schema::table('miembros', function (Blueprint $table) {
            // Agrega la columna nuevamente
            $table->unsignedBigInteger('proyecto_id')->nullable();
            // Vuelve a crear la restricción de clave foránea
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
        });
    }
    
};
