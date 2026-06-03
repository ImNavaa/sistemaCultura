<?php

namespace App\Http\Controllers;

use App\Models\AgoraArea;
use App\Models\AgoraReserva;
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
        $reservas = AgoraReserva::with('creadoPor')->get();

        $eventos = $reservas->map(function ($r) {
            $color = $r->getColorCalendario();

            $horaInicio = $r->hora_inicio ? $r->fecha->format('Y-m-d') . 'T' . $r->hora_inicio : $r->fecha->format('Y-m-d');
            $horaFin    = $r->hora_fin    ? $r->fecha->format('Y-m-d') . 'T' . $r->hora_fin    : null;

            $titulo = $r->titulo;
            if ($r->estado === 'tentativo') $titulo = '(Tentativo) ' . $titulo;
            if ($r->estado === 'cancelado') $titulo = '[Cancelado] ' . $titulo;

            $areas = [];
            if ($r->tipo === 'area' && !empty($r->areas_ids)) {
                $areas = AgoraArea::whereIn('id', $r->areas_ids)->pluck('nombre')->toArray();
            }

            return [
                'id'            => $r->id,
                'title'         => $titulo,
                'start'         => $horaInicio,
                'end'           => $horaFin,
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
                    'hora_inicio'  => $r->hora_inicio,
                    'hora_fin'     => $r->hora_fin,
                    'fecha'        => $r->fecha->format('Y-m-d'),
                    'areas_ids'    => $r->areas_ids ?? [],
                ],
            ];
        });

        return response()->json($eventos);
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
}
