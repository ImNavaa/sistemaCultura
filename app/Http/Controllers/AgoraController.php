<?php

namespace App\Http\Controllers;

use App\Models\AgoraArea;
use App\Models\AgoraReserva;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgoraController extends Controller
{
    // ── Calendario principal ─────────────────────────────────
    public function index()
    {
        $areas = AgoraArea::activas()->get();
        return view('agora.calendario', compact('areas'));
    }

    // ── API: eventos para FullCalendar ────────────────────────
    public function getReservas()
    {
        try {
            $reservas = AgoraReserva::with('creadoPor')->get();

            $eventos = $reservas->map(function ($r) {
                $color = $r->getColorCalendario();

                $fechaStr = $r->fecha instanceof \Carbon\Carbon
                    ? $r->fecha->format('Y-m-d')
                    : substr((string) $r->fecha, 0, 10);

                $horaInicio = $r->hora_inicio
                    ? $fechaStr . 'T' . substr($r->hora_inicio, 0, 5)
                    : $fechaStr;
                $horaFin = $r->hora_fin
                    ? $fechaStr . 'T' . substr($r->hora_fin, 0, 5)
                    : null;

                $titulo = $r->titulo;
                if ($r->estado === 'tentativo') $titulo = '(Tentativo) ' . $titulo;
                if ($r->estado === 'cancelado') $titulo = '[Cancelado] ' . $titulo;

                $areas = [];
                $areasIds = is_array($r->areas_ids) ? $r->areas_ids : [];
                if ($r->tipo === 'area' && !empty($areasIds)) {
                    $areas = AgoraArea::whereIn('id', $areasIds)->pluck('nombre')->toArray();
                }

                return [
                    'id'              => $r->id,
                    'title'           => $titulo,
                    'start'           => $horaInicio,
                    'end'             => $horaFin,
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'textColor'       => '#fff',
                    'extendedProps'   => [
                        'tipo'         => $r->tipo,
                        'organizador'  => $r->organizador,
                        'responsable'  => $r->responsable,
                        'telefono'     => $r->telefono_contacto,
                        'estado'       => $r->estado,
                        'descripcion'  => $r->descripcion,
                        'notas'        => $r->notas_internas,
                        'areas'        => $areas,
                        'hora_inicio'  => $r->hora_inicio ? substr($r->hora_inicio, 0, 5) : null,
                        'hora_fin'     => $r->hora_fin    ? substr($r->hora_fin, 0, 5)    : null,
                        'fecha'        => $fechaStr,
                        'areas_ids'    => $areasIds,
                    ],
                ];
            });

            return response()->json($eventos);

        } catch (\Exception $e) {
            \Log::error('AgoraController::getReservas — ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    // ── Crear reserva ─────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'            => 'required|string|max:255',
            'tipo'              => 'required|in:evento,fotografia,area',
            'organizador'       => 'required|string|max:255',
            'responsable'       => 'nullable|string|max:255',
            'telefono_contacto' => 'nullable|string|max:30',
            'fecha'             => 'required|date',
            'hora_inicio'       => 'nullable|date_format:H:i',
            'hora_fin'          => 'nullable|date_format:H:i|after:hora_inicio',
            'areas_ids'         => 'nullable|array',
            'areas_ids.*'       => 'exists:agora_areas,id',
            'descripcion'       => 'nullable|string',
            'notas_internas'    => 'nullable|string',
            'estado'            => 'required|in:confirmado,tentativo,cancelado',
        ]);

        $data['creado_por'] = Auth::id();

        if ($data['tipo'] !== 'area') {
            $data['areas_ids'] = null;
        }

        AgoraReserva::create($data);

        return response()->json(['success' => true]);
    }

    // ── Actualizar reserva ────────────────────────────────────
    public function update(Request $request, AgoraReserva $reserva)
    {
        $data = $request->validate([
            'titulo'            => 'required|string|max:255',
            'tipo'              => 'required|in:evento,fotografia,area',
            'organizador'       => 'required|string|max:255',
            'responsable'       => 'nullable|string|max:255',
            'telefono_contacto' => 'nullable|string|max:30',
            'fecha'             => 'required|date',
            'hora_inicio'       => 'nullable|date_format:H:i',
            'hora_fin'          => 'nullable|date_format:H:i',
            'areas_ids'         => 'nullable|array',
            'areas_ids.*'       => 'exists:agora_areas,id',
            'descripcion'       => 'nullable|string',
            'notas_internas'    => 'nullable|string',
            'estado'            => 'required|in:confirmado,tentativo,cancelado',
        ]);

        if ($data['tipo'] !== 'area') {
            $data['areas_ids'] = null;
        }

        $reserva->update($data);

        return response()->json(['success' => true]);
    }

    // ── Eliminar reserva ──────────────────────────────────────
    public function destroy(AgoraReserva $reserva)
    {
        $reserva->delete();
        return response()->json(['success' => true]);
    }

    // ── Drag & drop: mover fecha ──────────────────────────────
    public function moverFecha(Request $request, AgoraReserva $reserva)
    {
        $data = $request->validate([
            'fecha'      => 'required|date',
            'hora_inicio'=> 'nullable|date_format:H:i',
            'hora_fin'   => 'nullable|date_format:H:i',
        ]);

        $reserva->update($data);
        return response()->json(['success' => true]);
    }

    // ══ GESTIÓN DE ÁREAS ════════════════════════════════════

    public function areasIndex()
    {
        $areas = AgoraArea::orderBy('orden')->orderBy('nombre')->get();
        return view('agora.areas', compact('areas'));
    }

    public function areasStore(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'capacidad'   => 'nullable|integer|min:1',
            'orden'       => 'nullable|integer',
        ]);

        AgoraArea::create($data);

        return redirect()->route('agora.areas')->with('success', 'Área creada correctamente.');
    }

    public function areasUpdate(Request $request, AgoraArea $area)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'capacidad'   => 'nullable|integer|min:1',
            'activa'      => 'boolean',
            'orden'       => 'nullable|integer',
        ]);

        $data['activa'] = $request->boolean('activa');
        $area->update($data);

        return redirect()->route('agora.areas')->with('success', 'Área actualizada.');
    }

    public function areasDestroy(AgoraArea $area)
    {
        $area->delete();
        return redirect()->route('agora.areas')->with('success', 'Área eliminada.');
    }

    public function reporte(Request $request)
    {
        $request->validate([
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        $desde = $request->date('desde');
        $hasta = $request->date('hasta');

        $reservas = AgoraReserva::whereBetween('fecha', [$desde->format('Y-m-d'), $hasta->format('Y-m-d')])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        $areas = AgoraArea::all()->keyBy('id');

        $pdf = Pdf::loadView('agora.reporte-pdf', compact('reservas', 'areas', 'desde', 'hasta'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('reporte-agora-' . $desde->format('Y-m-d') . '-al-' . $hasta->format('Y-m-d') . '.pdf');
    }
}
