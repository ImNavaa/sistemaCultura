<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos', 'hora_inicio')) {
                $table->time('hora_inicio')->nullable()->after('fecha');
            }
            if (!Schema::hasColumn('eventos', 'hora_fin')) {
                $table->time('hora_fin')->nullable()->after('hora_inicio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio', 'hora_fin']);
        });
    }
};