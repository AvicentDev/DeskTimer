<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsuarioIdToTareasArchivadasTable extends Migration
{
    public function up()
    {
        Schema::table('tareas_archivadas', function (Blueprint $table) {
            // Añadir la columna usuario_id
            $table->unsignedBigInteger('usuario_id');

            // Añadir la clave foránea
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tareas_archivadas', function (Blueprint $table) {
            // Eliminar la clave foránea y la columna usuario_id
            $table->dropForeign(['usuario_id']);
            $table->dropColumn('usuario_id');
        });
    }
}
