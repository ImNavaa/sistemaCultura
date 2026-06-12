<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Asistente;
use App\Models\AsistenciaEvento;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'actividad_id' => 'required|exists:actividades,id',
            'asistente_id' => 'nullable|exists:asistentes,id',
            'nombre'       => 'required_without:asistente_id|nullable|string|max:100',
            'apellidos'    => 'required_without:asistente_id|nullable|string|max:100',
            'email'        => 'nullable|email',
            'telefono'     => 'nullable|string|max:20',
            'institucion'  => 'nullable|string|max:255',
            'ciudad'       => 'nullable|string|max:100',
        ]);

        $actividad = Actividad::findOrFail($request->actividad_id);

        if ($actividad->cupo_maximo) {
            $inscritos = $actividad->inscripcionesActivas()->count();
            if ($inscritos >= $actividad->cupo_maximo) {
                return back()->withErrors(['cupo' => 'El cupo de esta actividad está lleno.']);
            }
        }

        if ($request->asistente_id) {
            $asistente = Asistente::findOrFail($request->asistente_id);
        } else {
            $asistente = null;
            if ($request->email) {
                $asistente = Asistente::where('email', $request->email)->first();
            }
            if (! $asistente) {
                $asistente = Asistente::create([
                    'nombre'      => $request->nombre,
                    'apellidos'   => $request->apellidos,
                    'email'       => $request->email ?: null,
                    'telefono'    => $request->telefono ?: null,
                    'institucion' => $request->institucion ?: null,
                    'ciudad'      => $request->ciudad ?: null,
                ]);
            }
        }

        $existe = Inscripcion::where('actividad_id', $actividad->id)
            ->where('asistente_id', $asistente->id)
            ->exists();

        if ($existe) {
            return back()->withErrors(['duplicado' => "{$asistente->nombreCompleto()} ya está inscrito en esta actividad."]);
        }

        $folio = Inscripcion::generarFolio();
        Inscripcion::create([
            'folio'        => $folio,
            'actividad_id' => $actividad->id,
            'asistente_id' => $asistente->id,
            'estado'       => 'inscrito',
            'notas'        => $request->notas ?: null,
        ]);

        return back()->with('success', "Inscrito correctamente — Folio: {$folio}");
    }

    public function checkin(Request $request, Inscripcion $inscripcion)
    {
        if ($inscripcion->checkin) {
            return back()->withErrors(['checkin' => 'Ya se registró la asistencia de esta persona.']);
        }

        AsistenciaEvento::create([
            'inscripcion_id' => $inscripcion->id,
            'hora_checkin'   => now(),
            'metodo'         => 'manual',
            'validado_por'   => auth()->id(),
        ]);

        return back()->with('success', 'Asistencia registrada para ' . $inscripcion->asistente->nombreCompleto() . '.');
    }

    public function destroy(Inscripcion $inscripcion)
    {
        $inscripcion->delete();
        return back()->with('success', 'Inscripción eliminada.');
    }
}
