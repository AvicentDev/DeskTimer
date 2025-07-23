<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->foreignId('etiqueta_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('etiquetas')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('entrada_tiempo', function (Blueprint $table) {
            $table->dropForeign(['etiqueta_id']);
            $table->dropColumn('etiqueta_id');
        });
    }
};
