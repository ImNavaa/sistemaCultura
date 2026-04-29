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
        Schema::table('entregas', function (Blueprint $table) {
            $table->string('folio', 20)->unique()->after('id');
            $table->string('unidad_solicitante', 100)->nullable()->after('receptor');
        });
    }

    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropUnique(['folio']);
            $table->dropColumn(['folio', 'unidad_solicitante']);
        });
    }
};
