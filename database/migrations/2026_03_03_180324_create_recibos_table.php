<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();                                         // ID autoincremental
            $table->date('fecha');                                // Fecha del recibo
            $table->string('nombre_evento', 255);                // Nombre del evento
            $table->decimal('importe', 10, 2);                   // Importe del recibo
            $table->string('organizador', 255);                  // Quién organiza
            $table->text('concepto');                            // Concepto del recibo
            $table->timestamps();                                 // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibos');
    }
};
