<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaTiempoEtiquetaTable extends Migration
{
    public function up()
    {
        Schema::create('entrada_tiempo_etiqueta', function (Blueprint $table) {
            $table->foreignId('entrada_tiempo_id')
                  ->constrained('entrada_tiempos')
                  ->onDelete('cascade');
            $table->foreignId('etiqueta_id')
                  ->constrained('etiquetas')
                  ->onDelete('cascade');
            $table->primary(['entrada_tiempo_id', 'etiqueta_id']);
        });

        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->dropForeign(['etiqueta_id']);
            $table->dropColumn('etiqueta_id');
        });
    }

    public function down()
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->foreignId('etiqueta_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');
        });
        Schema::dropIfExists('entrada_tiempo_etiqueta');
    }
}
