<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeFieldsToSolicitudesTable extends Migration
{
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
     

            // Añadimos los nuevos campos:
            $table->timestamp('tiempo_inicio')->after('usuario_id');
            $table->timestamp('tiempo_fin')->nullable()->after('tiempo_inicio');
            $table->unsignedBigInteger('proyecto_id')->after('tiempo_fin');
            $table->text('descripcion')->nullable()->after('proyecto_id');

            $table->foreign('proyecto_id')
                  ->references('id')->on('proyectos')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
            $table->dropColumn(['tiempo_inicio', 'tiempo_fin', 'proyecto_id', 'descripcion']);
        });
    }
}
