<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('fecha');
            $table->enum('estado', [
                'a_tiempo',
                'tarde',
                'falta_justificada',
                'falta_injustificada',
                'cubriendo_evento',
                'horas_extra',
                'tiempo_compensatorio',
                'salida_temprana',
                'guardia',
            ]);
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->string('inmueble', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            // Un registro por empleado por día
            $table->unique(['user_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};