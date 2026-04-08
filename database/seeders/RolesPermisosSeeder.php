<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Módulos y acciones
        $modulos = [
            'calendario' => 'Calendario de Eventos',
            'oficios'    => 'Oficios',
            'recibos'    => 'Recibos',
            'asistencias'=> 'Asistencias',
            'usuarios'   => 'Empleados / Usuarios',
            'almacen'    => 'Almacén',
            'entregas'   => 'Entregas',
            'tiempo'     => 'Control de Tiempo',
        ];

        $acciones = [
            'ver'      => 'Ver',
            'crear'    => 'Crear',
            'editar'   => 'Editar',
            'eliminar' => 'Eliminar',
        ];

        // Crear permisos
        foreach ($modulos as $modulo => $labelModulo) {
            foreach ($acciones as $accion => $labelAccion) {
                Permiso::firstOrCreate(
                    ['modulo' => $modulo, 'accion' => $accion],
                    ['descripcion' => "{$labelAccion} {$labelModulo}"]
                );
            }
        }

        // Crear roles
        $roles = [
            [
                'nombre'      => 'super_admin',
                'descripcion' => 'Super Administrador — acceso total',
                'es_sistema'  => true,
                'permisos'    => 'todos',
            ],
            [
                'nombre'      => 'admin',
                'descripcion' => 'Administrador — todo menos eliminar usuarios',
                'es_sistema'  => true,
                'permisos'    => ['todos_menos' => ['usuarios.eliminar']],
            ],
            [
                'nombre'      => 'rh',
                'descripcion' => 'Recursos Humanos',
                'es_sistema'  => true,
                'permisos'    => [
                    'asistencias.ver', 'asistencias.crear',
                    'asistencias.editar', 'asistencias.eliminar',
                    'usuarios.ver', 'usuarios.crear', 'usuarios.editar',
                    'tiempo.ver', 'tiempo.crear', 'tiempo.editar',
                ],
            ],
            [
                'nombre'      => 'encargado_inmueble',
                'descripcion' => 'Encargado de Inmueble',
                'es_sistema'  => true,
                'permisos'    => [
                    'asistencias.ver', 'asistencias.crear', 'asistencias.editar',
                ],
            ],
            [
                'nombre'      => 'almacen',
                'descripcion' => 'Gestor de Almacén',
                'es_sistema'  => true,
                'permisos'    => [
                    'almacen.ver', 'almacen.crear', 'almacen.editar',
                    'entregas.ver', 'entregas.crear',
                ],
            ],
            [
                'nombre'      => 'consulta',
                'descripcion' => 'Solo consulta — sin edición',
                'es_sistema'  => true,
                'permisos'    => [
                    'calendario.ver', 'oficios.ver', 'recibos.ver',
                    'asistencias.ver', 'usuarios.ver', 'almacen.ver',
                ],
            ],
        ];

        $todosPermisos = Permiso::all();

        foreach ($roles as $rolData) {
            $rol = Rol::firstOrCreate(
                ['nombre' => $rolData['nombre']],
                [
                    'descripcion' => $rolData['descripcion'],
                    'es_sistema'  => $rolData['es_sistema'],
                ]
            );

            if ($rolData['permisos'] === 'todos') {
                $rol->permisos()->sync($todosPermisos->pluck('id'));
            } elseif (isset($rolData['permisos']['todos_menos'])) {
                $excluir = $rolData['permisos']['todos_menos'];
                $ids = $todosPermisos->filter(function($p) use ($excluir) {
                    return !in_array("{$p->modulo}.{$p->accion}", $excluir);
                })->pluck('id');
                $rol->permisos()->sync($ids);
            } else {
                $ids = $todosPermisos->filter(function($p) use ($rolData) {
                    return in_array("{$p->modulo}.{$p->accion}", $rolData['permisos']);
                })->pluck('id');
                $rol->permisos()->sync($ids);
            }
        }

        $this->command->info('✅ Roles y permisos creados correctamente.');
    }
}