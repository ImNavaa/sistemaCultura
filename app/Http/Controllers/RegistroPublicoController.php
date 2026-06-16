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
        if ($actividad->estado !== 'activo') {
            return back()->withErrors(['error' => 'Esta actividad no está disponible.']);
        }

        if ($actividad->cupo_maximo && $actividad->inscripcionesActivas()->count() >= $actividad->cupo_maximo) {
            return back()->withErrors(['error' => 'El cupo está lleno.']);
        }

        $config = $actividad->configFormulario();
        $campos = $config['campos'];

        // Reglas dinámicas según configuración
        $rules = [
            'nombre'    => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
        ];

        $campoRules = [
            'email'          => ['nullable', 'email', 'max:255'],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'edad'           => ['nullable', 'integer', 'min:1', 'max:120'],
            'genero'         => ['nullable', 'in:femenino,masculino,otro,prefiero_no_decir'],
            'institucion'    => ['nullable', 'string', 'max:255'],
            'ocupacion'      => ['nullable', 'string', 'max:255'],
            'ciudad'         => ['nullable', 'string', 'max:100'],
            'redes_sociales' => ['nullable', 'string', 'max:255'],
            'curp'           => ['nullable', 'string', 'max:18'],
        ];

        foreach ($campoRules as $campo => $baseRules) {
            if (($campos[$campo] ?? 'opcional') === 'oculto') continue;
            $reglas = $baseRules;
            if (($campos[$campo] ?? 'opcional') === 'requerido') {
                $reglas = array_map(fn($r) => $r === 'nullable' ? 'required' : $r, $reglas);
            }
            $rules[$campo] = $reglas;
        }

        // Preguntas extra
        foreach ($config['preguntas_extra'] as $i => $pregunta) {
            $rules["extra_{$i}"] = $pregunta['requerido'] ? 'required|string|max:500' : 'nullable|string|max:500';
        }

        $validated = $request->validate($rules);

        // Separar datos del asistente de respuestas extra
        $datosAsistente = array_intersect_key($validated, array_flip(
            ['nombre', 'apellidos', 'email', 'telefono', 'edad', 'genero', 'institucion', 'ocupacion', 'ciudad', 'curp']
        ));

        $respuestas = [];
        foreach ($config['preguntas_extra'] as $i => $pregunta) {
            $respuestas[] = [
                'pregunta' => $pregunta['label'],
                'respuesta' => $validated["extra_{$i}"] ?? null,
            ];
        }

        // Buscar o crear asistente
        $asistente = null;
        if (! empty($datosAsistente['email'])) {
            $asistente = Asistente::where('email', $datosAsistente['email'])->first();
        }

        if (! $asistente) {
            $asistente = Asistente::create($datosAsistente);
        } else {
            $asistente->update(array_filter($datosAsistente, fn($v) => $v !== null));
        }

        // Verificar duplicado
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
            'respuestas'   => $respuestas ?: null,
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
