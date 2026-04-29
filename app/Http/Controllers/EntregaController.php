<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Entrega;
use App\Services\AlmacenService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    public function __construct(protected AlmacenService $almacenService) {}

    public function index()
    {
        $entregas = Entrega::with(['detalles.articulo', 'responsable'])
            ->orderBy('fecha_entrega', 'desc')
            ->paginate(15);
        return view('entregas.index', compact('entregas'));
    }

    public function create()
    {
        $articulos = Articulo::orderBy('nombre')->get();
        return view('entregas.create', compact('articulos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receptor'           => 'required|string|max:255',
            'unidad_solicitante' => 'required|string|max:100',
            'fecha_entrega'      => 'required|date',
            'observaciones'      => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.articulo_id'=> 'required|exists:articulos,id',
            'items.*.cantidad'   => 'required|numeric|min:0.01',
        ], [
            'items.required'           => 'Debes agregar al menos un artículo.',
            'items.min'                => 'Debes agregar al menos un artículo.',
            'items.*.articulo_id.required' => 'Selecciona un artículo.',
            'items.*.cantidad.required'    => 'Ingresa una cantidad.',
            'items.*.cantidad.min'         => 'La cantidad debe ser mayor a 0.',
        ]);

        try {
            $cabecera = [
                'receptor'           => $request->receptor,
                'unidad_solicitante' => $request->unidad_solicitante,
                'fecha_entrega'      => $request->fecha_entrega,
                'observaciones'      => $request->observaciones,
                'responsable_id'     => auth()->id(),
                'folio'              => Entrega::generarFolio(),
            ];

            $entrega = $this->almacenService->registrarEntrega($cabecera, $request->items);

            return redirect()->route('entregas.index')
                ->with('success', "Entrega registrada correctamente. Folio: {$entrega->folio}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function pdf(Entrega $entrega)
    {
        $entrega->load(['detalles.articulo', 'responsable']);

        $pdf = Pdf::loadView('entregas.pdf', compact('entrega'))
            ->setPaper('letter', 'portrait');

        return $pdf->download('vale-salida-' . $entrega->folio . '.pdf');
    }

    public function destroy(Entrega $entrega)
    {
        $entrega->load('detalles');
        $this->almacenService->cancelarEntrega($entrega);
        return back()->with('success', 'Entrega cancelada y stock restaurado.');
    }
}
