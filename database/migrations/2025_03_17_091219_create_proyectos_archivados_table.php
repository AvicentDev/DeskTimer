<?php

// database/migrations/xxxx_xx_xx_create_proyectos_archivados_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectosArchivadosTable extends Migration
{
    public function up()
    {
        Schema::create('proyectos_archivados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id'); // Referencia al proyecto original
            $table->unsignedBigInteger('usuario_id');  // Referencia al usuario
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_archivo')->useCurrent();
            $table->timestamps();

            // Clave foránea para usuario (ajusta el nombre de la tabla si es necesario)
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyectos_archivados');
    }
}
