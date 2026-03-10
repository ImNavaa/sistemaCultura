<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_tiempo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('fecha');
            $table->enum('tipo', [
                'horas_extra',        // trabajó más horas de su jornada
                'evento_especial',    // trabajó en evento especial
                'dia_descanso',       // trabajó en su día de descanso
                'apoyo_adicional',    // cubrió turno o apoyo
                'salida_temprana',    // salió antes de tiempo
                'horas_libres',       // tomó horas libres
                'dia_libre',          // tomó día libre completo
            ]);
            $table->enum('categoria', ['favor', 'compensacion']);
            $table->decimal('horas', 5, 2); // horas con decimales ej: 1.5 = 1hr 30min
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_tiempo');
    }
};