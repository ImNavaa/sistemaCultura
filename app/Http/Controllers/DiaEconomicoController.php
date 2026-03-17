<?php

namespace App\Http\Controllers;

use App\Models\DiaEconomico;
use App\Models\User;
use Illuminate\Http\Request;

class DiaEconomicoController extends Controller
{
    public function index()
    {
        $empleados = User::with(['diasEconomicos' => function($q) {
            $q->orderBy('anio', 'desc');
        }])->orderBy('name')->get();

        $anioActual = now()->year;

        return view('dias_economicos.index', compact('empleados', 'anioActual'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|exists:users,id',
            'anio'           => 'required|integer|min:2020|max:2040',
            'dias_asignados' => 'required|integer|min:0|max:365',
            'observaciones'  => 'nullable|string',
        ]);

        DiaEconomico::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'anio'    => $request->anio,
            ],
            [
                'dias_asignados' => $request->dias_asignados,
                'observaciones'  => $request->observaciones,
            ]
        );

        return back()->with('success', 'Días económicos actualizados.');
    }

    public function update(Request $request, DiaEconomico $diasEconomico)
    {
        $request->validate([
            'dias_asignados' => 'required|integer|min:0',
            'dias_usados'    => 'required|integer|min:0',
            'observaciones'  => 'nullable|string',
        ]);

        $diasEconomico->update($request->only(['dias_asignados', 'dias_usados', 'observaciones']));

        return back()->with('success', 'Actualizado correctamente.');
    }

    public function destroy(DiaEconomico $diasEconomico)
    {
        $diasEconomico->delete();
        return back()->with('success', 'Eliminado.');
    }
}