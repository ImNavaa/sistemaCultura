<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // ✅ Agregar columnas nuevas a asistencias (si no existen)
        Schema::table('asistencias', function (Blueprint $table) {
            if (!Schema::hasColumn('asistencias', 'evento_id')) {
                $table->unsignedBigInteger('evento_id')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('asistencias', 'fecha_fin')) {
                $table->date('fecha_fin')->nullable()->after('hora_salida');
            }
            if (!Schema::hasColumn('asistencias', 'folio_documento')) {
                $table->string('folio_documento', 100)->nullable()->after('fecha_fin');
            }
            if (!Schema::hasColumn('asistencias', 'fecha_compensatorio')) {
                $table->date('fecha_compensatorio')->nullable()->after('folio_documento');
            }
        });

        // ✅ Modificar ENUM solo en MySQL (SQLite no lo necesita, usa string)
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE asistencias MODIFY COLUMN estado ENUM(
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
        }

        // ✅ Foreign key solo en MySQL
        if ($driver === 'mysql') {
            Schema::table('asistencias', function (Blueprint $table) {
                $table->foreign('evento_id')
                      ->references('id')
                      ->on('eventos')
                      ->nullOnDelete();
            });
        }

        // ✅ Tabla días económicos
        if (!Schema::hasTable('dias_economicos')) {
            Schema::create('dias_economicos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('anio');
                $table->integer('dias_asignados')->default(0);
                $table->integer('dias_usados')->default(0);
                $table->text('observaciones')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'anio']);
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        Schema::table('asistencias', function (Blueprint $table) use ($driver) {
            if ($driver === 'mysql' && Schema::hasColumn('asistencias', 'evento_id')) {
                $table->dropForeign(['evento_id']);
            }
            $cols = ['evento_id', 'fecha_fin', 'folio_documento', 'fecha_compensatorio'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('asistencias', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('dias_economicos');
    }
};
