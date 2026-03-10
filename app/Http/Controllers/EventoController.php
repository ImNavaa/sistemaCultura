<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Oficio;
use App\Models\Recibo;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    // Vista principal del calendario
    public function index()
    {
        return view('eventos.calendario');
    }

    // Devuelve eventos en formato JSON para FullCalendar
    public function getEventos()
    {
        $eventos = Evento::all()->map(function ($evento) {
            $color = match ($evento->tipo) {
                'oficio'  => '#3a7bd5',
                'recibo'  => '#7b3ad5',
                'ambos'   => '#e8c547',
                'ninguno' => '#555555',
            };

            // Buscar oficio relacionado para obtener las horas
            $oficio = \App\Models\Oficio::where('nombre_evento', $evento->nombre_evento)
                ->where('fecha', $evento->fecha)
                ->first();

            $start = $evento->fecha->format('Y-m-d');
            $end   = null;

            if ($oficio && $oficio->hora_inicio) {
                $start = $evento->fecha->format('Y-m-d') . 'T' . $oficio->hora_inicio;
                if ($oficio->hora_fin) {
                    $end = $evento->fecha->format('Y-m-d') . 'T' . $oficio->hora_fin;
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
                    'organizador' => $evento->organizador,
                    'tipo'        => $evento->tipo,
                    'hora_inicio' => $oficio->hora_inicio ?? null,
                    'hora_fin'    => $oficio->hora_fin ?? null,
                ],
            ];
        });

        return response()->json($eventos);
    }

    // Guarda un nuevo evento
    public function store(Request $request)
    {
        $request->validate([
            'fecha'         => 'required|date',
            'nombre_evento' => 'required|string|max:255',
            'organizador'   => 'required|string|max:255',
            'tipo'          => 'required|in:oficio,recibo,ambos,ninguno',
        ]);

        $evento = Evento::create($request->only(['fecha', 'nombre_evento', 'organizador', 'tipo']));

        // Si tiene oficio, crearlo también
        if (in_array($request->tipo, ['oficio', 'ambos'])) {
            Oficio::create([
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

        // Si tiene recibo, crearlo también
        if (in_array($request->tipo, ['recibo', 'ambos'])) {
            Recibo::create([
                'fecha'         => $request->fecha,
                'nombre_evento' => $request->nombre_evento,
                'organizador'   => $request->organizador,
                'importe'       => $request->importe,
                'concepto'      => $request->concepto,
            ]);
        }

        return response()->json(['success' => true, 'evento' => $evento]);
    }

    // Elimina un evento
    public function destroy(Evento $evento)
    {
        $evento->delete();
        return response()->json(['success' => true]);
    }
}
