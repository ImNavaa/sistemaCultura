<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\User;
use App\Services\AlmacenService;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function __construct(protected AlmacenService $almacenService) {}

    public function index()
    {
        $articulos = Articulo::with('responsable')->orderBy('nombre')->get();
        return view('almacen.index', compact('articulos'));
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        return view('almacen.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string',
            'cantidad_actual'=> 'required|numeric|min:0',
            'unidad'         => 'required|string|max:50',
            'responsable_id' => 'required|exists:users,id',
        ]);

        Articulo::create($request->all());

        return redirect()->route('almacen.index')
            ->with('success', 'Artículo registrado correctamente.');
    }

    public function show(Articulo $almacen)
    {
        $entregas = $almacen->entregas()->with('responsable')
            ->orderBy('fecha_entrega', 'desc')->get();
        return view('almacen.show', compact('almacen', 'entregas'));
    }

    public function edit(Articulo $almacen)
    {
        $usuarios = User::orderBy('name')->get();
        return view('almacen.edit', compact('almacen', 'usuarios'));
    }

    public function update(Request $request, Articulo $almacen)
    {
        $request->validate([
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string',
            'cantidad_actual'=> 'required|numeric|min:0',
            'unidad'         => 'required|string|max:50',
            'responsable_id' => 'required|exists:users,id',
        ]);

        $almacen->update($request->all());

        return redirect()->route('almacen.index')
            ->with('success', 'Artículo actualizado correctamente.');
    }

    public function destroy(Articulo $almacen)
    {
        $almacen->delete();
        return redirect()->route('almacen.index')
            ->with('success', 'Artículo eliminado correctamente.');
    }
}