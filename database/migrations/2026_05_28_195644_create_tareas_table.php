<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'cancelada'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('creado_por')->constrained('users')->restrictOnDelete();
            $table->date('fecha_limite')->nullable();
            $table->timestamp('fecha_completada')->nullable();
            $table->foreignId('completado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
