<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldos_tiempo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->decimal('horas_favor', 6, 2)->default(0);      // total horas generadas
            $table->decimal('horas_compensadas', 6, 2)->default(0); // total horas usadas
            $table->decimal('saldo', 6, 2)->default(0);             // favor - compensadas
            $table->timestamp('ultima_actualizacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldos_tiempo');
    }
};