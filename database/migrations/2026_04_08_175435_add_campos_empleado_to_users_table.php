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
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefono', 20)->nullable()->after('email');
            $table->string('cargo', 100)->nullable()->after('telefono');
            $table->string('horario', 100)->nullable()->after('cargo');
            $table->string('dias_laborales', 255)->nullable()->after('horario');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telefono', 'cargo', 'horario', 'dias_laborales']);
        });
    }
};
