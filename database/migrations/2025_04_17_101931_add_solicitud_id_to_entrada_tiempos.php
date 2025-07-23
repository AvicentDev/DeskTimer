<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSolicitudIdToEntradaTiempos extends Migration
{
    public function up()
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->unsignedBigInteger('solicitud_id')->nullable()->after('id');
            $table->foreign('solicitud_id')
                  ->references('id')->on('solicitudes')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('entrada_tiempos', function (Blueprint $table) {
            $table->dropForeign(['solicitud_id']);
            $table->dropColumn('solicitud_id');
        });
    }
}
