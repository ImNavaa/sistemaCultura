<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TareaController extends Controller
{
    public function store(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'prioridad'    => 'required|in:baja,media,alta,urgente',
            'estado'       => 'required|in:pendiente,en_progreso,completada,cancelada',
            'asignado_a'   => 'nullable|exists:users,id',
            'fecha_limite' => 'nullable|date',
        ]);

        $data['proyecto_id'] = $proyecto->id;
        $data['creado_por']  = Auth::id();

        if ($data['estado'] === 'completada') {
            $data['fecha_completada'] = now();
            $data['completado_por']   = Auth::id();
        }

        Tarea::create($data);

        return redirect()->route('proyectos.show', $proyecto)
            ->with('success', 'Tarea creada correctamente.');
    }

    public function update(Request $request, Tarea $tarea)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'prioridad'    => 'required|in:baja,media,alta,urgente',
            'estado'       => 'required|in:pendiente,en_progreso,completada,cancelada',
            'asignado_a'   => 'nullable|exists:users,id',
            'fecha_limite' => 'nullable|date',
        ]);

        if ($data['estado'] === 'completada' && $tarea->estado !== 'completada') {
            $data['fecha_completada'] = now();
            $data['completado_por']   = Auth::id();
        } elseif ($data['estado'] !== 'completada') {
            $data['fecha_completada'] = null;
            $data['completado_por']   = null;
        }

        $tarea->update($data);

        return redirect()->route('proyectos.show', $tarea->proyecto_id)
            ->with('success', 'Tarea actualizada correctamente.');
    }

    public function updateEstado(Request $request, Tarea $tarea)
    {
        $user = Auth::user();

        $esAdmin = $user->puede('proyectos', 'editar');
        $esAsignado = (int) $tarea->asignado_a === (int) $user->id;

        if (!$esAdmin && !$esAsignado) {
            return response()->json(['error' => 'Sin autorización'], 403);
        }

        $data = $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
        ]);

        if ($data['estado'] === 'completada' && $tarea->estado !== 'completada') {
            $data['fecha_completada'] = now();
            $data['completado_por']   = $user->id;
        } elseif ($data['estado'] !== 'completada') {
            $data['fecha_completada'] = null;
            $data['completado_por']   = null;
        }

        $tarea->update($data);

        return response()->json(['success' => true, 'estado' => $tarea->estado]);
    }

    public function destroy(Tarea $tarea)
    {
        $proyectoId = $tarea->proyecto_id;
        $tarea->delete();

        return redirect()->route('proyectos.show', $proyectoId)
            ->with('success', 'Tarea eliminada correctamente.');
    }
}
