<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public static function moduloNombres(): array
    {
        return [
            'calendario'     => ['Calendario de Eventos',     'bi-calendar3'],
            'agora'          => ['Ágora',                     'bi-building'],
            'oficios'        => ['Oficios',                   'bi-file-earmark-text'],
            'recibos'        => ['Recibos',                   'bi-receipt'],
            'asistencias'    => ['Asistencias (empleados)',   'bi-person-check'],
            'usuarios'       => ['Empleados / Usuarios / RH', 'bi-people'],
            'tiempo'         => ['Control de Tiempo',         'bi-clock-history'],
            'almacen'        => ['Almacén',                   'bi-box-seam'],
            'entregas'       => ['Entregas',                  'bi-box-arrow-right'],
            'proyectos'      => ['Proyectos y Tareas',        'bi-kanban'],
            'act_asistentes' => ['Registro de Asistentes',   'bi-person-badge'],
        ];
    }

    public function index()
    {
        $roles        = Rol::with('permisos')->get();
        $permisos     = Permiso::all()->groupBy('modulo');
        $moduloNombres = static::moduloNombres();

        return view('roles.index', compact('roles', 'permisos', 'moduloNombres'));
    }

    public function update(Request $request, Rol $rol)
    {
        if ($rol->nombre === 'super_admin') {
            return back()->withErrors(['error' => 'Los permisos del Super Admin no se pueden modificar.']);
        }

        $seleccionados = $request->input('permisos', []);
        $rol->permisos()->sync(
            Permiso::whereIn('id', $seleccionados)->pluck('id')
        );

        return back()->with('success', 'Permisos del rol «' . ucfirst(str_replace('_', ' ', $rol->nombre)) . '» actualizados correctamente.');
    }
}
