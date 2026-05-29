<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['activo', 'pausado', 'completado', 'cancelado'])->default('activo');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->string('color', 7)->nullable();
            $table->foreignId('creador_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('proyecto_usuario', function (Blueprint $table) {
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['proyecto_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_usuario');
        Schema::dropIfExists('proyectos');
    }
};
