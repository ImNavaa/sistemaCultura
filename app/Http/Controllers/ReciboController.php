<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use App\Http\Requests\StoreReciboRequest;
use App\Http\Requests\UpdateReciboRequest;

class ReciboController extends Controller
{
    public function index()
    {
        $recibos = Recibo::latest()->paginate(10);
        return view('recibos.index', compact('recibos'));
    }

    public function create()
    {
        return view('recibos.create');
    }

    public function store(StoreReciboRequest $request)
    {
        $datos = $request->validated();

        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('recibos', 'public');
        }

        Recibo::create($datos);
        return redirect()->route('recibos.index')
            ->with('success', 'Recibo creado correctamente.');
    }

    public function show(Recibo $recibo)
    {
        return view('recibos.show', compact('recibo'));
    }

    public function edit(Recibo $recibo)
    {
        return view('recibos.edit', compact('recibo'));
    }

    public function update(UpdateReciboRequest $request, Recibo $recibo) // <-- R mayúscula
    {
        $datos = $request->validated();

        if ($request->hasFile('foto')) {
            if ($recibo->foto) {
                \Storage::disk('public')->delete($recibo->foto);
            }
            $datos['foto'] = $request->file('foto')->store('recibos', 'public');
        }

        $recibo->update($datos);
        return redirect()->route('recibos.index')
            ->with('success', 'Recibo actualizado correctamente.'); // <-- corregido
    }

    public function destroy(Recibo $recibo)
    {
        $recibo->delete();
        return redirect()->route('recibos.index')
            ->with('success', 'Recibo eliminado correctamente.');
    }
}