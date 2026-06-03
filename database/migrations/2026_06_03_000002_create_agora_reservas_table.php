<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agora_reservas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['evento', 'fotografia', 'area'])->default('evento');
            // evento = función completa del Ágora
            // fotografia = sesión fotográfica
            // area = solo algunas áreas específicas

            $table->string('organizador');
            $table->string('responsable')->nullable();
            $table->string('telefono_contacto')->nullable();

            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            // Áreas involucradas (solo para tipo = 'area'); JSON array de IDs
            $table->json('areas_ids')->nullable();

            $table->text('descripcion')->nullable();
            $table->text('notas_internas')->nullable();

            $table->enum('estado', ['confirmado', 'tentativo', 'cancelado'])->default('confirmado');

            $table->foreignId('creado_por')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agora_reservas');
    }
};
