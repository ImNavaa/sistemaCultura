<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Asistente;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class RegistroPublicoController extends Controller
{
    public function index()
    {
        $actividades = Actividad::where('estado', 'activo')
            ->withCount(['inscripciones as inscritos_count' => fn($q) => $q->where('estado', 'inscrito')])
            ->orderBy('fecha_inicio')
            ->get();

        return view('registro.index', compact('actividades'));
    }

    public function form(Actividad $actividad)
    {
        if (! in_array($actividad->estado, ['activo'])) {
            return redirect()->route('registro.index')
                ->with('error', 'Esta actividad no está disponible para registro.');
        }

        if ($actividad->cupo_maximo) {
            $inscritos = $actividad->inscripcionesActivas()->count();
            if ($inscritos >= $actividad->cupo_maximo) {
                return redirect()->route('registro.index')
                    ->with('error', 'El cupo de esta actividad está lleno.');
            }
        }

        return view('registro.form', compact('actividad'));
    }

    public function store(Request $request, Actividad $actividad)
    {
        if (! in_array($actividad->estado, ['activo'])) {
            return back()->withErrors(['error' => 'Esta actividad no está disponible.']);
        }

        if ($actividad->cupo_maximo && $actividad->inscripcionesActivas()->count() >= $actividad->cupo_maximo) {
            return back()->withErrors(['error' => 'El cupo está lleno.']);
        }

        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'apellidos'   => 'required|string|max:100',
            'email'       => 'nullable|email|max:255',
            'telefono'    => 'nullable|string|max:20',
            'institucion' => 'nullable|string|max:255',
            'ocupacion'   => 'nullable|string|max:255',
            'ciudad'      => 'nullable|string|max:100',
            'edad'        => 'nullable|integer|min:1|max:120',
            'genero'      => 'nullable|in:femenino,masculino,otro,prefiero_no_decir',
            'curp'        => 'nullable|string|max:18',
        ]);

        // Buscar asistente por email si lo proporcionó, si no crear uno nuevo
        $asistente = null;
        if (! empty($data['email'])) {
            $asistente = Asistente::where('email', $data['email'])->first();
        }

        if (! $asistente) {
            $asistente = Asistente::create($data);
        } else {
            // Actualizar datos si ya existe
            $asistente->update(array_filter($data, fn($v) => $v !== null));
        }

        // Verificar si ya está inscrito
        $yaInscrito = Inscripcion::where('actividad_id', $actividad->id)
            ->where('asistente_id', $asistente->id)
            ->exists();

        if ($yaInscrito) {
            return redirect()->route('registro.confirmacion', $actividad)
                ->with('info', 'Ya estabas registrado en esta actividad. ¡Te esperamos!')
                ->with('nombre', $asistente->nombreCompleto());
        }

        $folio = Inscripcion::generarFolio();
        Inscripcion::create([
            'folio'        => $folio,
            'actividad_id' => $actividad->id,
            'asistente_id' => $asistente->id,
            'estado'       => 'inscrito',
        ]);

        return redirect()->route('registro.confirmacion', $actividad)
            ->with('success', '¡Registro exitoso!')
            ->with('folio', $folio)
            ->with('nombre', $asistente->nombreCompleto());
    }

    public function confirmacion(Actividad $actividad)
    {
        return view('registro.confirmacion', compact('actividad'));
    }
}
