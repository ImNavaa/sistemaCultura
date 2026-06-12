<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Asistente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $tipo   = $request->input('tipo');
        $estado = $request->input('estado');
        $q      = $request->input('q');

        $query = Actividad::with('creador')
            ->withCount(['inscripciones as inscritos_count' => fn($q) => $q->where('estado', 'inscrito')])
            ->orderBy('fecha_inicio', 'desc');

        if ($tipo)   $query->where('tipo', $tipo);
        if ($estado) $query->where('estado', $estado);
        if ($q)      $query->where('nombre', 'like', "%$q%");

        $actividades = $query->paginate(20)->withQueryString();

        $stats = [
            'total'      => Actividad::count(),
            'activas'    => Actividad::where('estado', 'activo')->count(),
            'mes'        => Actividad::whereMonth('fecha_inicio', now()->month)
                               ->whereYear('fecha_inicio', now()->year)->count(),
            'asistentes' => Asistente::count(),
        ];

        return view('actividades.index', compact('actividades', 'stats', 'tipo', 'estado', 'q'));
    }

    public function create()
    {
        return view('actividades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'tipo'         => 'required|in:evento,curso,taller,conferencia,capacitacion',
            'instructor'   => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_inicio'  => 'nullable',
            'hora_fin'     => 'nullable',
            'ubicacion'    => 'nullable|string|max:255',
            'modalidad'    => 'required|in:presencial,virtual,hibrido',
            'cupo_maximo'  => 'nullable|integer|min:1',
            'estado'       => 'required|in:borrador,activo,lleno,cancelado,finalizado',
        ]);

        $data['codigo']            = Actividad::generarCodigo();
        $data['creado_por']        = auth()->id();
        $data['campos_formulario'] = $this->parsearConfigFormulario($request);

        Actividad::create($data);

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function show(Actividad $actividad)
    {
        $inscripciones = $actividad->inscripciones()
            ->with(['asistente', 'checkin'])
            ->orderBy('created_at')
            ->get();

        $totalInscritos  = $inscripciones->where('estado', 'inscrito')->count();
        $totalAsistieron = $inscripciones->filter(fn($i) => $i->checkin !== null)->count();
        $porcentaje      = $totalInscritos > 0 ? round(($totalAsistieron / $totalInscritos) * 100) : 0;

        return view('actividades.show', compact(
            'actividad', 'inscripciones', 'totalInscritos', 'totalAsistieron', 'porcentaje'
        ));
    }

    public function edit(Actividad $actividad)
    {
        return view('actividades.edit', compact('actividad'));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'tipo'         => 'required|in:evento,curso,taller,conferencia,capacitacion',
            'instructor'   => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_inicio'  => 'nullable',
            'hora_fin'     => 'nullable',
            'ubicacion'    => 'nullable|string|max:255',
            'modalidad'    => 'required|in:presencial,virtual,hibrido',
            'cupo_maximo'  => 'nullable|integer|min:1',
            'estado'       => 'required|in:borrador,activo,lleno,cancelado,finalizado',
        ]);

        $data['campos_formulario'] = $this->parsearConfigFormulario($request);

        $actividad->update($data);

        return redirect()->route('actividades.show', $actividad)
            ->with('success', 'Actividad actualizada correctamente.');
    }

    private function parsearConfigFormulario(Request $request): array
    {
        $camposValidos  = ['email', 'telefono', 'edad', 'genero', 'institucion', 'ocupacion', 'ciudad', 'curp'];
        $estadosValidos = ['requerido', 'opcional', 'oculto'];

        $campos = [];
        foreach ($camposValidos as $campo) {
            $val = $request->input("campo_{$campo}", 'opcional');
            $campos[$campo] = in_array($val, $estadosValidos) ? $val : 'opcional';
        }

        $preguntasExtra = [];
        $labels   = $request->input('pregunta_label', []);
        $tipos    = $request->input('pregunta_tipo', []);
        $opciones = $request->input('pregunta_opciones', []);
        $reqs     = $request->input('pregunta_requerido', []);

        foreach ($labels as $i => $label) {
            $label = trim($label);
            if ($label === '') continue;

            $tipo = in_array($tipos[$i] ?? '', ['texto', 'texto_largo', 'seleccion']) ? $tipos[$i] : 'texto';

            $opcsArr = [];
            if ($tipo === 'seleccion' && isset($opciones[$i])) {
                $opcsArr = array_values(array_filter(array_map('trim', explode("\n", $opciones[$i]))));
            }

            $preguntasExtra[] = [
                'label'     => $label,
                'tipo'      => $tipo,
                'opciones'  => $opcsArr,
                'requerido' => isset($reqs[$i]),
            ];
        }

        return [
            'campos'          => $campos,
            'preguntas_extra' => $preguntasExtra,
        ];
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();
        return redirect()->route('actividades.index')->with('success', 'Actividad eliminada.');
    }

    public function exportPdf(Actividad $actividad)
    {
        $inscripciones = $actividad->inscripciones()
            ->with(['asistente', 'checkin'])
            ->where('estado', 'inscrito')
            ->orderBy('created_at')
            ->get();

        $pdf = Pdf::loadView('actividades.reporte-pdf', compact('actividad', 'inscripciones'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('lista-asistentes-' . $actividad->codigo . '.pdf');
    }

    public function exportCsv(Actividad $actividad)
    {
        $inscripciones = $actividad->inscripciones()
            ->with(['asistente', 'checkin'])
            ->where('estado', 'inscrito')
            ->orderBy('created_at')
            ->get();

        $filename = 'asistentes-' . $actividad->codigo . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($inscripciones) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['#', 'Folio', 'Nombre', 'Apellidos', 'Email', 'Teléfono', 'Institución', 'Ciudad', 'Asistió', 'Hora Check-in']);
            foreach ($inscripciones as $i => $insc) {
                fputcsv($out, [
                    $i + 1,
                    $insc->folio,
                    $insc->asistente->nombre,
                    $insc->asistente->apellidos,
                    $insc->asistente->email ?? '',
                    $insc->asistente->telefono ?? '',
                    $insc->asistente->institucion ?? '',
                    $insc->asistente->ciudad ?? '',
                    $insc->checkin ? 'Sí' : 'No',
                    $insc->checkin ? $insc->checkin->hora_checkin->format('d/m/Y H:i') : '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
