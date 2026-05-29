<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        $query = Proyecto::withCount(['tareas', 'tareasCompletadas'])
            ->with(['creador', 'miembros'])
            ->orderByRaw("FIELD(estado, 'activo', 'pausado', 'completado', 'cancelado')")
            ->orderBy('fecha_limite');

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->buscar . '%');
        }

        $proyectos = $query->get();

        return view('proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        $usuarios = User::where('tiene_acceso', true)
            ->orderBy('nombre')
            ->get();
        $colores = Proyecto::coloresDisponibles();

        return view('proyectos.create', compact('usuarios', 'colores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'estado'       => 'required|in:activo,pausado,completado,cancelado',
            'fecha_inicio' => 'nullable|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_inicio',
            'color'        => 'nullable|string|max:7',
            'miembros'     => 'nullable|array',
            'miembros.*'   => 'exists:users,id',
        ]);

        $data['creador_id'] = Auth::id();
        $miembros = $data['miembros'] ?? [];
        unset($data['miembros']);

        $proyecto = Proyecto::create($data);
        $proyecto->miembros()->sync($miembros);

        return redirect()->route('proyectos.show', $proyecto)
            ->with('success', 'Proyecto creado correctamente.');
    }

    public function show(Proyecto $proyecto)
    {
        $proyecto->load([
            'tareas.asignado',
            'tareas.creadoPor',
            'miembros',
            'creador',
        ]);

        $usuarios = User::where('tiene_acceso', true)->orderBy('nombre')->get();

        return view('proyectos.show', compact('proyecto', 'usuarios'));
    }

    public function edit(Proyecto $proyecto)
    {
        $usuarios = User::where('tiene_acceso', true)->orderBy('nombre')->get();
        $colores  = Proyecto::coloresDisponibles();
        $miembrosIds = $proyecto->miembros->pluck('id')->toArray();

        return view('proyectos.edit', compact('proyecto', 'usuarios', 'colores', 'miembrosIds'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'estado'       => 'required|in:activo,pausado,completado,cancelado',
            'fecha_inicio' => 'nullable|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_inicio',
            'color'        => 'nullable|string|max:7',
            'miembros'     => 'nullable|array',
            'miembros.*'   => 'exists:users,id',
        ]);

        $miembros = $data['miembros'] ?? [];
        unset($data['miembros']);

        $proyecto->update($data);
        $proyecto->miembros()->sync($miembros);

        return redirect()->route('proyectos.show', $proyecto)
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }
}
