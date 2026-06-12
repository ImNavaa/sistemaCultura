<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asistentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->unsignedSmallInteger('edad')->nullable();
            $table->enum('genero', ['femenino', 'masculino', 'otro', 'prefiero_no_decir'])->nullable();
            $table->string('institucion')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('curp', 18)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistentes');
    }
};
