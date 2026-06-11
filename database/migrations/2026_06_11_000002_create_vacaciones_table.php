<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('anio');
            $table->integer('dias_asignados')->default(15);
            $table->integer('dias_usados')->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
