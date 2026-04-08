<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    // Ver permisos de un usuario
    public function index(User $usuario)
    {
        $modulos  = Permiso::select('modulo')->distinct()->pluck('modulo');
        $permisos = Permiso::all()->groupBy('modulo');
        $rol      = $usuario->rol;
        $permisosRol    = $rol?->permisos->pluck('id')->toArray() ?? [];
        $permisosExtra  = $usuario->permisosExtra->keyBy('id');
        $roles    = Rol::all();

        return view('permisos.index', compact(
            'usuario', 'modulos', 'permisos',
            'permisosRol', 'permisosExtra', 'roles'
        ));
    }

    // Actualizar rol y permisos extra del usuario
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'rol_id'    => 'nullable|exists:roles,id',
            'permisos'  => 'nullable|array',
            'permisos.*'=> 'exists:permisos,id',
        ]);

        // Actualizar rol
        $usuario->update(['rol_id' => $request->rol_id]);

        // Sincronizar permisos extra (true = extra, false = bloqueado)
        $permisosSeleccionados = $request->permisos ?? [];
        $todosPermisos = Permiso::pluck('id');

        $sync = [];
        foreach ($todosPermisos as $id) {
            $sync[$id] = ['permitido' => in_array($id, $permisosSeleccionados)];
        }

        $usuario->permisosExtra()->sync($sync);

        return back()->with('success', 'Permisos actualizados correctamente.');
    }
}