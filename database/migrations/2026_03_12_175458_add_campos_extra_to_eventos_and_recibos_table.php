<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->string('autoriza', 255)->nullable()->after('organizador');
        });

        Schema::table('recibos', function (Blueprint $table) {
            $table->string('numero_recibo', 100)->nullable()->after('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('autoriza');
        });

        Schema::table('recibos', function (Blueprint $table) {
            $table->dropColumn('numero_recibo');
        });
    }
};