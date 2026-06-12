<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('asistente_id')->constrained('asistentes')->cascadeOnDelete();
            $table->enum('estado', ['inscrito', 'cancelado'])->default('inscrito');
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->unique(['actividad_id', 'asistente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
