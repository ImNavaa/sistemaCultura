<?php

namespace App\Http\Controllers;

use App\Models\Asistente;
use Illuminate\Http\Request;

class AsistenteController extends Controller
{
    public function index(Request $request)
    {
        $q     = $request->get('q');
        $query = Asistente::withCount('inscripciones')->orderBy('nombre');

        if ($q) {
            $query->where(function ($sq) use ($q) {
                $sq->where('nombre', 'like', "%$q%")
                   ->orWhere('apellidos', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%")
                   ->orWhere('institucion', 'like', "%$q%");
            });
        }

        $asistentes = $query->paginate(25)->withQueryString();
        return view('asistentes.index', compact('asistentes', 'q'));
    }

    public function show(Asistente $asistente)
    {
        $inscripciones = $asistente->inscripciones()
            ->with(['actividad', 'checkin'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('asistentes.show', compact('asistente', 'inscripciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'apellidos'   => 'required|string|max:100',
            'email'       => 'nullable|email',
            'telefono'    => 'nullable|string|max:20',
            'edad'        => 'nullable|integer|min:1|max:120',
            'genero'      => 'nullable|in:femenino,masculino,otro,prefiero_no_decir',
            'institucion' => 'nullable|string|max:255',
            'ocupacion'   => 'nullable|string|max:255',
            'curp'        => 'nullable|string|max:18',
            'ciudad'      => 'nullable|string|max:100',
            'notas'       => 'nullable|string',
        ]);

        $asistente = Asistente::create($data);

        if ($request->expectsJson()) {
            return response()->json(['asistente' => $asistente]);
        }

        return back()->with('success', 'Asistente registrado correctamente.');
    }

    public function update(Request $request, Asistente $asistente)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'apellidos'   => 'required|string|max:100',
            'email'       => 'nullable|email',
            'telefono'    => 'nullable|string|max:20',
            'edad'        => 'nullable|integer|min:1|max:120',
            'genero'      => 'nullable|in:femenino,masculino,otro,prefiero_no_decir',
            'institucion' => 'nullable|string|max:255',
            'ocupacion'   => 'nullable|string|max:255',
            'curp'        => 'nullable|string|max:18',
            'ciudad'      => 'nullable|string|max:100',
            'notas'       => 'nullable|string',
        ]);

        $asistente->update($data);
        return back()->with('success', 'Datos actualizados correctamente.');
    }

    public function destroy(Asistente $asistente)
    {
        $asistente->delete();
        return back()->with('success', 'Asistente eliminado.');
    }

    public function buscar(Request $request)
    {
        $q = $request->get('q', '');
        $asistentes = Asistente::where('nombre', 'like', "%$q%")
            ->orWhere('apellidos', 'like', "%$q%")
            ->orWhere('email', 'like', "%$q%")
            ->limit(10)
            ->get(['id', 'nombre', 'apellidos', 'email', 'institucion', 'ciudad']);

        return response()->json($asistentes);
    }
}
