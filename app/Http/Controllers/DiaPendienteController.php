<?php

namespace App\Http\Controllers;

use App\Models\DiaPendiente;
use App\Models\User;
use Illuminate\Http\Request;

class DiaPendienteController extends Controller
{
    public function index(Request $request)
    {
        $filtroUser = $request->get('empleado');

        $query = DiaPendiente::with(['empleado', 'registrador'])
            ->orderBy('fecha_generacion', 'desc');

        if ($filtroUser) {
            $query->where('user_id', $filtroUser);
        }

        $registros = $query->get();
        $empleados = User::visibles()->orderBy('name')->get();

        // Conteo por empleado
        $pendientesPorEmpleado = DiaPendiente::where('estado', 'pendiente')
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return view('dias-pendientes.index', compact('registros', 'empleados', 'filtroUser', 'pendientesPorEmpleado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'fecha_generacion' => 'required|date',
            'motivo'           => 'required|string|max:255',
        ]);

        DiaPendiente::create([
            'user_id'          => $request->user_id,
            'fecha_generacion' => $request->fecha_generacion,
            'motivo'           => $request->motivo,
            'estado'           => 'pendiente',
            'registrado_por'   => auth()->id(),
        ]);

        return back()->with('success', 'Día pendiente registrado correctamente.');
    }

    public function usar(Request $request, DiaPendiente $diaPendiente)
    {
        if ($diaPendiente->estado === 'utilizado') {
            return back()->withErrors(['error' => 'Este día ya fue utilizado.']);
        }

        $request->validate(['fecha_uso' => 'nullable|date']);

        $diaPendiente->update([
            'estado'    => 'utilizado',
            'fecha_uso' => $request->fecha_uso ?? now()->toDateString(),
        ]);

        return back()->with('success', 'Día marcado como utilizado.');
    }

    public function destroy(DiaPendiente $diaPendiente)
    {
        $diaPendiente->delete();
        return back()->with('success', 'Registro eliminado.');
    }
}
