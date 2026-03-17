<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Actualizar tabla asistencias con nuevos estados y campos
        Schema::table('asistencias', function (Blueprint $table) {
            // Cambiar ENUM con todos los estados
            \DB::statement("ALTER TABLE asistencias MODIFY COLUMN estado ENUM(
                'a_tiempo',
                'tarde',
                'falta_justificada',
                'falta_injustificada',
                'cubriendo_evento',
                'horas_extra',
                'tiempo_compensatorio',
                'salida_temprana',
                'guardia',
                'vacaciones',
                'dia_economico',
                'incapacidad',
                'cita_medica'
            ) NOT NULL");

            // Campos adicionales
            $table->foreignId('evento_id')->nullable()->after('estado')
                  ->constrained('eventos')->nullOnDelete();
            $table->date('fecha_fin')->nullable()->after('hora_salida');     // para vacaciones/incapacidad
            $table->string('folio_documento', 100)->nullable()->after('fecha_fin'); // folio incapacidad/cita
            $table->date('fecha_compensatorio')->nullable()->after('folio_documento'); // cuándo tomará tiempo comp
        });

        // Tabla de días económicos por empleado
        Schema::create('dias_economicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('anio');
            $table->integer('dias_asignados')->default(0);
            $table->integer('dias_usados')->default(0);
            $table->integer('dias_pendientes')->virtualAs('dias_asignados - dias_usados');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropForeign(['evento_id']);
            $table->dropColumn(['evento_id', 'fecha_fin', 'folio_documento', 'fecha_compensatorio']);
        });

        Schema::dropIfExists('dias_economicos');
    }
};