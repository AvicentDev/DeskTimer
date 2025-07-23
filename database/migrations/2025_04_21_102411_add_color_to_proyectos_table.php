<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('proyectos', function (Blueprint $table) {
        $table->string('color', 7)
              ->default('#2196F3')   // color por defecto si quieres
              ->after('nombre');
    });
}

public function down(): void
{
    Schema::table('proyectos', function (Blueprint $table) {
        $table->dropColumn('color');
    });
}

};
