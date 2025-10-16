<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeFieldsToSolicitudesTable extends Migration
{
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            // Añadimos las columnas solo si no existen
            if (!Schema::hasColumn('solicitudes', 'tiempo_inicio')) {
                $table->timestamp('tiempo_inicio')->after('usuario_id');
            }

            if (!Schema::hasColumn('solicitudes', 'tiempo_fin')) {
                $table->timestamp('tiempo_fin')->nullable()->after('tiempo_inicio');
            }

            if (!Schema::hasColumn('solicitudes', 'proyecto_id')) {
                $table->unsignedBigInteger('proyecto_id')->after('tiempo_fin');
                $table->foreign('proyecto_id')
                    ->references('id')->on('proyectos')
                    ->onDelete('cascade');
            }

            if (!Schema::hasColumn('solicitudes', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('proyecto_id');
            }
        });
    }

    public function down()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            // Eliminamos solo si existen
            if (Schema::hasColumn('solicitudes', 'proyecto_id')) {
                $table->dropForeign(['proyecto_id']);
            }

            $columns = ['tiempo_inicio', 'tiempo_fin', 'proyecto_id', 'descripcion'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('solicitudes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
