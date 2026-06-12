<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $modulos = [
        'calendario'     => 'Calendario de Eventos',
        'agora'          => 'Ágora',
        'oficios'        => 'Oficios',
        'recibos'        => 'Recibos',
        'asistencias'    => 'Asistencias (empleados)',
        'usuarios'       => 'Empleados / Usuarios / RH',
        'tiempo'         => 'Control de Tiempo',
        'almacen'        => 'Almacén',
        'entregas'       => 'Entregas',
        'proyectos'      => 'Proyectos y Tareas',
        'act_asistentes' => 'Registro de Asistentes',
    ];

    private array $acciones = [
        'ver'      => 'Ver',
        'crear'    => 'Crear',
        'editar'   => 'Editar',
        'eliminar' => 'Eliminar',
    ];

    public function up(): void
    {
        $now  = now();
        $rows = [];

        foreach ($this->modulos as $modulo => $labelModulo) {
            foreach ($this->acciones as $accion => $labelAccion) {
                $rows[] = [
                    'modulo'      => $modulo,
                    'accion'      => $accion,
                    'descripcion' => "{$labelAccion} {$labelModulo}",
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        // insertOrIgnore respeta el UNIQUE(modulo, accion) — no duplica
        DB::table('permisos')->insertOrIgnore($rows);

        // Dar al rol super_admin todos los permisos que le falten
        $superAdmin = DB::table('roles')->where('nombre', 'super_admin')->first();
        if ($superAdmin) {
            $todosIds    = DB::table('permisos')->pluck('id');
            $yaAsignados = DB::table('rol_permiso')
                ->where('rol_id', $superAdmin->id)
                ->pluck('permiso_id');

            $faltantes = $todosIds->diff($yaAsignados);
            $insert    = $faltantes->map(fn($pid) => [
                'rol_id'     => $superAdmin->id,
                'permiso_id' => $pid,
            ])->values()->all();

            if ($insert) {
                DB::table('rol_permiso')->insertOrIgnore($insert);
            }
        }

        // Dar al rol admin todos los permisos menos usuarios.eliminar
        $admin = DB::table('roles')->where('nombre', 'admin')->first();
        if ($admin) {
            $excluir     = DB::table('permisos')
                ->where('modulo', 'usuarios')->where('accion', 'eliminar')
                ->pluck('id');
            $todosIds    = DB::table('permisos')->pluck('id');
            $elegibles   = $todosIds->diff($excluir);
            $yaAsignados = DB::table('rol_permiso')
                ->where('rol_id', $admin->id)
                ->pluck('permiso_id');
            $faltantes   = $elegibles->diff($yaAsignados);
            $insert      = $faltantes->map(fn($pid) => [
                'rol_id'     => $admin->id,
                'permiso_id' => $pid,
            ])->values()->all();
            if ($insert) {
                DB::table('rol_permiso')->insertOrIgnore($insert);
            }
        }
    }

    public function down(): void
    {
        // No elimina permisos existentes al hacer rollback
    }
};
