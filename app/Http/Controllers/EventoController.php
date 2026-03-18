<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Oficio;
use App\Models\Recibo;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        return view('eventos.calendario');
    }

    public function getEventos()
    {
        // Cargar relaciones eager para mejor rendimiento
        $eventos = Evento::with(['oficio', 'recibo'])->get()->map(function ($evento) {

            $color = match ($evento->tipo) {
                'oficio'  => '#3a7bd5',
                'recibo'  => '#7b3ad5',
                'ambos'   => '#e8c547',
                'ninguno' => '#555555',
                default   => '#555555',
            };

            // Usar relaciones en lugar de buscar por nombre+fecha
            $oficio = $evento->oficio;
            $recibo = $evento->recibo;

            // Fallback para registros viejos sin evento_id
            if (!$oficio && in_array($evento->tipo, ['oficio', 'ambos'])) {
                $oficio = Oficio::where('nombre_evento', $evento->nombre_evento)
                    ->whereDate('fecha', $evento->fecha)
                    ->first();
                // Relacionar automáticamente si se encuentra
                if ($oficio) $oficio->update(['evento_id' => $evento->id]);
            }

            if (!$recibo && in_array($evento->tipo, ['recibo', 'ambos'])) {
                $recibo = Recibo::where('nombre_evento', $evento->nombre_evento)
                    ->whereDate('fecha', $evento->fecha)
                    ->first();
                // Relacionar automáticamente si se encuentra
                if ($recibo) $recibo->update(['evento_id' => $evento->id]);
            }

            // Horas: prioridad evento → oficio
            $horaInicio = $evento->hora_inicio ?? $oficio?->hora_inicio ?? null;
            $horaFin    = $evento->hora_fin    ?? $oficio?->hora_fin    ?? null;

            // Formatear fechas para FullCalendar
            try {
                $fechaStr = $evento->fecha->format('Y-m-d');
            } catch (\Exception $e) {
                $fechaStr = now()->format('Y-m-d');
            }

            $start = $fechaStr;
            $end   = null;

            if ($horaInicio) {
                $horaInicioStr = is_string($horaInicio)
                    ? substr($horaInicio, 0, 5)
                    : $horaInicio;
                $start = $fechaStr . 'T' . $horaInicioStr;

                if ($horaFin) {
                    $horaFinStr = is_string($horaFin)
                        ? substr($horaFin, 0, 5)
                        : $horaFin;
                    $end = $fechaStr . 'T' . $horaFinStr;
                }
            }

            return [
                'id'              => $evento->id,
                'title'           => $evento->nombre_evento,
                'start'           => $start,
                'end'             => $end,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'organizador'    => $evento->organizador,
                    'autoriza'       => $evento->autoriza,
                    'tipo'           => $evento->tipo,
                    'hora_inicio'    => $horaInicio ? substr($horaInicio, 0, 5) : null,
                    'hora_fin'       => $horaFin    ? substr($horaFin, 0, 5)    : null,
                    'numero_oficio'  => $oficio?->numero_oficio  ?? null,
                    'numero_recibo'  => $recibo?->numero_recibo  ?? null,
                    'cobrado'        => $oficio?->cobrado        ?? null,
                    'monto_cobrado'  => $oficio?->monto_cobrado  ?? null,
                    'importe'        => $recibo?->importe        ?? null,
                    'concepto'       => $recibo?->concepto       ?? null,
                ],
            ];
        });

        return response()->json($eventos);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'fecha'         => 'required|date',
                'nombre_evento' => 'required|string|max:255',
                'organizador'   => 'required|string|max:255',
                'tipo'          => 'required|in:oficio,recibo,ambos,ninguno',
                'hora_inicio'   => 'nullable|date_format:H:i',
                'hora_fin'      => 'nullable|date_format:H:i',
            ]);

            $evento = Evento::create($request->only([
                'fecha', 'nombre_evento', 'organizador',
                'autoriza', 'tipo', 'hora_inicio', 'hora_fin',
            ]));

            if (in_array($request->tipo, ['oficio', 'ambos'])) {
                Oficio::create([
                    'evento_id'     => $evento->id,
                    'fecha'         => $request->fecha,
                    'nombre_evento' => $request->nombre_evento,
                    'organizador'   => $request->organizador,
                    'numero_oficio' => $request->numero_oficio,
                    'cobrado'       => $request->cobrado === 'si',
                    'monto_cobrado' => $request->monto_cobrado ?? null,
                    'hora_inicio'   => $request->hora_inicio ?? null,
                    'hora_fin'      => $request->hora_fin ?? null,
                ]);
            }

            if (in_array($request->tipo, ['recibo', 'ambos'])) {
                Recibo::create([
                    'evento_id'     => $evento->id,
                    'fecha'         => $request->fecha,
                    'numero_recibo' => $request->numero_recibo,
                    'nombre_evento' => $request->nombre_evento,
                    'organizador'   => $request->organizador,
                    'importe'       => $request->importe,
                    'concepto'      => $request->concepto,
                ]);
            }

            return response()->json(['success' => true, 'evento' => $evento]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Evento $evento)
    {
        try {
            $request->validate([
                'fecha'         => 'required|date',
                'nombre_evento' => 'sometimes|string|max:255',
                'organizador'   => 'sometimes|string|max:255',
                'autoriza'      => 'nullable|string|max:255',
                'tipo'          => 'sometimes|in:oficio,recibo,ambos,ninguno',
                'hora_inicio'   => 'nullable|date_format:H:i',
                'hora_fin'      => 'nullable|date_format:H:i',
            ]);

            $evento->update($request->only([
                'fecha', 'nombre_evento', 'organizador',
                'autoriza', 'tipo', 'hora_inicio', 'hora_fin',
            ]));

            // Actualizar oficio relacionado
            if ($evento->oficio) {
                $evento->oficio->update([
                    'fecha'      => $request->fecha,
                    'hora_inicio'=> $request->hora_inicio,
                    'hora_fin'   => $request->hora_fin,
                ]);
            }

            // Actualizar recibo relacionado
            if ($evento->recibo) {
                $evento->recibo->update([
                    'fecha' => $request->fecha,
                ]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Evento $evento)
    {
        $evento->delete();
        return response()->json(['success' => true]);
    }
}