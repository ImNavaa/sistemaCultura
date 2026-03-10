<?php

namespace App\Http\Controllers;

use App\Models\Oficio;
use App\Http\Requests\StoreOficioRequest;
use App\Http\Requests\UpdateOficioRequest;

class OficioController extends Controller
{
    public function index()
    {
        $oficios = Oficio::latest()->paginate(10);
        return view('oficios.index', compact('oficios'));
    }

    public function create()
    {
        return view('oficios.create');
    }

    public function store(StoreOficioRequest $request)
    {
        $datos = $request->validated();

        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('oficios', 'public');
        }

        Oficio::create($datos);
        return redirect()->route('oficios.index')
            ->with('success', 'Oficio creado correctamente.');
    }

    public function show(Oficio $oficio)
    {
        return view('oficios.show', compact('oficio'));
    }

    public function edit(Oficio $oficio)
    {
        return view('oficios.edit', compact('oficio'));
    }

    public function update(UpdateOficioRequest $request, Oficio $oficio)
    {
        $datos = $request->validated();

        if ($request->hasFile('foto')) {
            if ($oficio->foto) {
                \Storage::disk('public')->delete($oficio->foto);
            }
            $datos['foto'] = $request->file('foto')->store('oficios', 'public');
        }

        $oficio->update($datos);
        return redirect()->route('oficios.index')
            ->with('success', 'Oficio actualizado correctamente.');
    }

    public function destroy(Oficio $oficio)
    {
        $oficio->delete();
        return redirect()->route('oficios.index')
            ->with('success', 'Oficio eliminado correctamente.');
    }
}
