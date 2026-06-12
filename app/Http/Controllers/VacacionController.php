<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vacacion;
use Illuminate\Http\Request;

class VacacionController extends Controller
{
    public function index(Request $request)
    {
        $anio = (int) $request->get('anio', now()->year);
        $anios = range(now()->year - 2, now()->year + 1);

        $empleados = User::visibles()->with([
            'vacaciones' => fn($q) => $q->where('anio', $anio),
        ])->orderBy('name')->get();

        return view('vacaciones.index', compact('empleados', 'anio', 'anios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|exists:users,id',
            'anio'           => 'required|integer|min:2020|max:2100',
            'dias_asignados' => 'required|integer|min:0|max:365',
            'observaciones'  => 'nullable|string|max:500',
        ]);

        Vacacion::updateOrCreate(
            ['user_id' => $request->user_id, 'anio' => $request->anio],
            [
                'dias_asignados' => $request->dias_asignados,
                'observaciones'  => $request->observaciones,
            ]
        );

        return back()->with('success', 'Vacaciones asignadas correctamente.');
    }

    public function usarDias(Request $request, Vacacion $vacacion)
    {
        $request->validate([
            'dias'           => 'required|integer|min:1',
            'observaciones'  => 'nullable|string|max:300',
        ]);

        $disponibles = $vacacion->dias_asignados - $vacacion->dias_usados;
        if ($request->dias > $disponibles) {
            return back()->withErrors(['usar_error_' . $vacacion->id => "Solo hay {$disponibles} día(s) disponibles."]);
        }

        $vacacion->increment('dias_usados', $request->dias);
        if ($request->filled('observaciones')) {
            $vacacion->update(['observaciones' => $vacacion->observaciones . "\n" . now()->format('d/m/Y') . ': ' . $request->observaciones]);
        }

        return back()->with('success', 'Días de vacaciones registrados correctamente.');
    }

    public function destroy(Vacacion $vacacion)
    {
        $vacacion->delete();
        return back()->with('success', 'Registro de vacaciones eliminado.');
    }
}
