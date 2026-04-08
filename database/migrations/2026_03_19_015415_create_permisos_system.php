<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // super_admin, admin, rh, etc
            $table->string('descripcion')->nullable();
            $table->boolean('es_sistema')->default(false); // no se puede eliminar
            $table->timestamps();
        });

        // Tabla de permisos por módulo y acción
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('modulo');        // calendario, oficios, etc
            $table->string('accion');        // ver, crear, editar, eliminar
            $table->string('descripcion')->nullable();
            $table->timestamps();
            $table->unique(['modulo', 'accion']);
        });

        // Permisos por rol
        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
            $table->primary(['rol_id', 'permiso_id']);
        });

        // Rol del usuario
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rol_id')) {
                $table->foreignId('rol_id')->nullable()->after('tiene_acceso')
                      ->constrained('roles')->nullOnDelete();
            }
        });

        // Permisos extra por usuario (sobrescriben al rol)
        Schema::create('user_permiso', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
            $table->boolean('permitido')->default(true); // true=extra, false=bloqueado
            $table->primary(['user_id', 'permiso_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
            $table->dropColumn('rol_id');
        });
        Schema::dropIfExists('user_permiso');
        Schema::dropIfExists('rol_permiso');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};