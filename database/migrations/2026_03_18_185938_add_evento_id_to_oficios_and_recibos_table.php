<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            if (!Schema::hasColumn('oficios', 'evento_id')) {
                $table->unsignedBigInteger('evento_id')->nullable()->after('id');
            }
        });

        Schema::table('recibos', function (Blueprint $table) {
            if (!Schema::hasColumn('recibos', 'evento_id')) {
                $table->unsignedBigInteger('evento_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->dropColumn('evento_id');
        });
        Schema::table('recibos', function (Blueprint $table) {
            $table->dropColumn('evento_id');
        });
    }
};