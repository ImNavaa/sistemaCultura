<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Entrega;
use App\Models\User;
use App\Services\AlmacenService;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    public function __construct(protected AlmacenService $almacenService) {}

    public function index()
    {
        $entregas = Entrega::with(['articulo', 'responsable'])
            ->orderBy('fecha_entrega', 'desc')
            ->paginate(15);
        return view('entregas.index', compact('entregas'));
    }

    public function create()
    {
        $articulos = Articulo::orderBy('nombre')->get();
        $usuarios  = User::orderBy('name')->get();
        return view('entregas.create', compact('articulos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'articulo_id'    => 'required|exists:articulos,id',
            'cantidad'       => 'required|numeric|min:0.01',
            'receptor'       => 'required|string|max:255',
            'fecha_entrega'  => 'required|date',
            'responsable_id' => 'required|exists:users,id',
            'observaciones'  => 'nullable|string',
        ]);

        try {
            $this->almacenService->registrarEntrega($request->all());
            return redirect()->route('entregas.index')
                ->with('success', 'Entrega registrada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Entrega $entrega)
    {
        $this->almacenService->cancelarEntrega($entrega);
        return back()->with('success', 'Entrega cancelada y stock restaurado.');
    }
}