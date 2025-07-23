<?php

// database/migrations/xxxx_xx_xx_create_tareas_archivadas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTareasArchivadasTable extends Migration
{
    public function up()
    {
        Schema::create('tareas_archivadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tarea_id'); // referencia a la tarea original
            $table->unsignedBigInteger('proyecto_id')->nullable(); // si la tarea pertenece a un proyecto
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            // Otros campos necesarios
            $table->timestamp('fecha_archivo')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tareas_archivadas');
    }
}

