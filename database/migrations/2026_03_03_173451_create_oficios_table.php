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
        Schema::create('oficios', function (Blueprint $table) {
            $table->id();                                              // ID autoincremental
            $table->date('fecha');                                     // Fecha del evento
            $table->string('nombre_evento', 255);                     // Nombre del evento
            $table->string('numero_oficio', 100)->unique();           // Número de oficio (único)
            $table->boolean('cobrado')->default(false);               // ¿Se cobró? (true/false)
            $table->decimal('monto_cobrado', 10, 2)->nullable();      // Cuánto se cobró (null si no se cobró)
            $table->string('organizador', 255);                       // Quién organiza
            $table->timestamps();                                      // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficios');
    }
};
