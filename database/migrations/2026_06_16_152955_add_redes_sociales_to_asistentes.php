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
        Schema::table('asistentes', function (Blueprint $table) {
            $table->string('redes_sociales')->nullable()->after('ciudad');
        });
    }

    public function down(): void
    {
        Schema::table('asistentes', function (Blueprint $table) {
            $table->dropColumn('redes_sociales');
        });
    }
};
