<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->time('hora_inicio')->nullable()->after('fecha');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->string('foto')->nullable()->after('organizador');
        });

        Schema::table('recibos', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('organizador');
        });
    }

    public function down(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio', 'hora_fin', 'foto']);
        });

        Schema::table('recibos', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};