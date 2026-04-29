<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrega_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained('entregas')->onDelete('cascade');
            $table->foreignId('articulo_id')->constrained('articulos')->onDelete('restrict');
            $table->decimal('cantidad', 10, 2);
            $table->timestamps();
        });

        // Migrar registros existentes al nuevo esquema
        DB::statement('
            INSERT INTO entrega_detalles (entrega_id, articulo_id, cantidad, created_at, updated_at)
            SELECT id, articulo_id, cantidad, created_at, updated_at
            FROM entregas
            WHERE articulo_id IS NOT NULL
        ');

        // Eliminar columnas antiguas de entregas
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropForeign(['articulo_id']);
            $table->dropColumn(['articulo_id', 'cantidad']);
        });
    }

    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->foreignId('articulo_id')->nullable()->constrained('articulos')->onDelete('restrict');
            $table->decimal('cantidad', 10, 2)->nullable();
        });

        Schema::dropIfExists('entrega_detalles');
    }
};
