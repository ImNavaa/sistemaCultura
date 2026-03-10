<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RegistroTiempo;
use App\Models\SaldoTiempo;
use App\Services\TiempoService;
use Illuminate\Http\Request;

class TiempoController extends Controller
{
    protected TiempoService $tiempoService;

    public function __construct(TiempoService $tiempoService)
    {
        $this->tiempoService = $tiempoService;
    }

    // Lista todos los empleados con su saldo
    public function index()
    {
        $empleados = User::with('saldoTiempo')->get();
        return view('tiempo.index', compact('empleados'));
    }

    // Ver detalle de un empleado
    public function show(User $user)
    {
        $registros = RegistroTiempo::where('user_id', $user->id)
            ->orderBy('fecha', 'desc')
            ->get();

        $saldo = SaldoTiempo::where('user_id', $user->id)->first();

        return view('tiempo.show', compact('user', 'registros', 'saldo'));
    }

    // Formulario para nuevo registro
    public function create()
    {
        $empleados = User::orderBy('name')->get();
        return view('tiempo.create', compact('empleados'));
    }

    // Guardar nuevo registro
    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'fecha'       => 'required|date',
            'tipo'        => 'required|string',
            'categoria'   => 'required|in:favor,compensacion',
            'horas'       => 'required|numeric|min:0.25',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $empleado = User::findOrFail($request->user_id);

        try {
            if ($request->categoria === 'favor') {
                $this->tiempoService->registrarHorasFavor($empleado, $request->all());
            } else {
                $this->tiempoService->registrarCompensacion($empleado, $request->all());
            }

            return redirect()->route('tiempo.show', $empleado)
                ->with('success', 'Registro guardado correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // Eliminar un registro y recalcular saldo
    public function destroy(RegistroTiempo $tiempo)
    {
        $empleado = $tiempo->empleado;
        $tiempo->delete();
        $this->tiempoService->recalcularSaldo($empleado);

        return back()->with('success', 'Registro eliminado y saldo actualizado.');
    }
}