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
        Schema::table('actividades', function (Blueprint $table) {
            $table->json('campos_formulario')->nullable()->after('estado');
        });

        Schema::table('inscripciones', function (Blueprint $table) {
            $table->json('respuestas')->nullable()->after('notas');
        });
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn('campos_formulario');
        });

        Schema::table('inscripciones', function (Blueprint $table) {
            $table->dropColumn('respuestas');
        });
    }
};
