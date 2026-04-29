<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\DiaEconomico;
use App\Models\Evento;
use App\Models\RegistroTiempo;
use App\Models\User;
use App\Services\TiempoService;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    protected TiempoService $tiempoService;

    public function __construct(TiempoService $tiempoService)
    {
        $this->tiempoService = $tiempoService;
    }

    public function index()
    {
        $hoy  = today();
        $anio = $hoy->year;

        // Excluir super_admin del tablero de asistencia
        $empleados = User::whereDoesntHave('rol', fn($q) => $q->where('nombre', 'super_admin'))
            ->with(['saldoTiempo', 'diasEconomicosAnio'])
            ->orderBy('name')
            ->get();

        // Asistencia activa: registro de hoy O registro multi-día vigente (vacaciones, incapacidad, etc.)
        $asistencias = Asistencia::where(function ($q) use ($hoy) {
                $q->whereDate('fecha', $hoy)
                  ->orWhere(function ($q2) use ($hoy) {
                      $q2->where('fecha', '<=', $hoy)
                         ->where('fecha_fin', '>=', $hoy);
                  });
            })
            ->whereIn('user_id', $empleados->pluck('id'))
            ->with('evento')
            ->orderByDesc('fecha')
            ->get()
            ->keyBy('user_id');

        // Adjuntar a cada empleado su asistencia activa
        $empleados->each(fn($emp) => $emp->asistenciaActiva = $asistencias->get($emp->id));

        $stats = [
            'total'        => $empleados->count(),
            'presentes'    => $asistencias->whereIn('estado', ['a_tiempo', 'tarde', 'cubriendo_evento', 'horas_extra', 'guardia'])->count(),
            'faltas'       => $asistencias->whereIn('estado', ['falta_justificada', 'falta_injustificada'])->count(),
            'ausencias'    => $asistencias->whereIn('estado', ['vacaciones', 'dia_economico', 'incapacidad', 'cita_medica'])->count(),
            'sin_registro' => $empleados->filter(fn($e) => !$e->asistenciaActiva)->count(),
        ];

        $eventosHoy = Evento::whereDate('fecha', $hoy)->get();
        $etiquetas  = Asistencia::etiquetas();

        return view('asistencias.index', compact(
            'empleados', 'stats', 'hoy', 'eventosHoy', 'etiquetas', 'anio'
        ));
    }

    public function store(Request $request)
    {
        try {
            if ($request->hora_entrada) {
                $request->merge(['hora_entrada' => substr($request->hora_entrada, 0, 5)]);
            }
            if ($request->hora_salida) {
                $request->merge(['hora_salida' => substr($request->hora_salida, 0, 5)]);
            }

            $request->validate([
                'user_id'              => 'required|exists:users,id',
                'fecha'                => 'required|date',
                'estado'               => 'required|in:' . implode(',', array_keys(Asistencia::etiquetas())),
                'hora_entrada'         => 'nullable|date_format:H:i',
                'hora_salida'          => 'nullable|date_format:H:i',
                'evento_id'            => 'nullable|exists:eventos,id',
                'fecha_fin'            => 'nullable|date|after_or_equal:fecha',
                'folio_documento'      => 'nullable|string|max:100',
                'fecha_compensatorio'  => 'nullable|date',
                'inmueble'             => 'nullable|string|max:255',
                'observaciones'        => 'nullable|string',
            ]);

            $asistencia = Asistencia::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'fecha'   => $request->fecha,
                ],
                [
                    'estado'               => $request->estado,
                    'evento_id'            => $request->evento_id,
                    'hora_entrada'         => $request->hora_entrada         ?: null,
                    'hora_salida'          => $request->hora_salida          ?: null,
                    'fecha_fin'            => $request->fecha_fin            ?: null,
                    'folio_documento'      => $request->folio_documento      ?: null,
                    'fecha_compensatorio'  => $request->fecha_compensatorio  ?: null,
                    'inmueble'             => $request->inmueble,
                    'observaciones'        => $request->observaciones,
                    'registrado_por'       => User::orderBy('id')->first()->id,
                ]
            );

            $empleado = User::find($request->user_id);

            // ✅ HORAS EXTRA / CUBRIENDO EVENTO / GUARDIA → tiempo a favor
            if (
                in_array($request->estado, ['horas_extra', 'cubriendo_evento', 'guardia']) &&
                $request->hora_entrada && $request->hora_salida
            ) {
                $horas       = $this->calcularHoras($request->hora_entrada, $request->hora_salida);
                $tipoMapeado = $this->mapearTipo($request->estado);

                if ($horas > 0) {
                    RegistroTiempo::where('user_id', $empleado->id)
                        ->where('fecha', $request->fecha)
                        ->where('categoria', 'favor')
                        ->where('tipo', $tipoMapeado)
                        ->delete();

                    $eventoNombre = $request->evento_id
                        ? Evento::find($request->evento_id)?->nombre_evento
                        : null;

                    $this->tiempoService->registrarHorasFavor($empleado, [
                        'fecha'       => $request->fecha,
                        'tipo'        => $tipoMapeado,
                        'horas'       => $horas,
                        'descripcion' => trim(
                            ucfirst(str_replace('_', ' ', $request->estado)) .
                            ($eventoNombre ? ": {$eventoNombre}" : "") .
                            " ({$request->hora_entrada} - {$request->hora_salida})" .
                            ($request->inmueble ? " en {$request->inmueble}" : "")
                        ),
                    ]);
                }
            }

            // ✅ TIEMPO COMPENSATORIO → descuenta saldo de horas
            if (
                $request->estado === 'tiempo_compensatorio' &&
                $request->hora_entrada && $request->hora_salida
            ) {
                $horas       = $this->calcularHoras($request->hora_entrada, $request->hora_salida);
                $tipoMapeado = $this->mapearTipo($request->estado);

                if ($horas > 0) {
                    RegistroTiempo::where('user_id', $empleado->id)
                        ->where('fecha', $request->fecha)
                        ->where('categoria', 'compensacion')
                        ->where('tipo', $tipoMapeado)
                        ->delete();

                    $fechaComp = $request->fecha_compensatorio
                        ? " (tomará el {$request->fecha_compensatorio})"
                        : "";

                    $this->tiempoService->registrarCompensacion($empleado, [
                        'fecha'       => $request->fecha,
                        'tipo'        => $tipoMapeado,
                        'horas'       => $horas,
                        'descripcion' => "Tiempo compensatorio {$request->hora_entrada} - {$request->hora_salida}{$fechaComp}",
                    ]);
                }
            }

            // ✅ DÍA ECONÓMICO → descuenta de días económicos
            if ($request->estado === 'dia_economico') {
                $this->usarDiaEconomico($empleado, $request->fecha);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user)
    {
        $anio        = request('anio', now()->year);
        $asistencias = Asistencia::where('user_id', $user->id)
            ->orderBy('fecha', 'desc')
            ->paginate(30);

        $resumen = [];
        foreach (array_keys(Asistencia::etiquetas()) as $estado) {
            $resumen[$estado] = Asistencia::where('user_id', $user->id)
                ->where('estado', $estado)->count();
        }

        $diasEconomicos = DiaEconomico::where('user_id', $user->id)
            ->orderBy('anio', 'desc')->get();

        $saldo = $user->saldoTiempo;

        return view('asistencias.show', compact(
            'user', 'asistencias', 'resumen', 'diasEconomicos', 'saldo'
        ));
    }

    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return response()->json(['success' => true]);
    }

    // Descontar un día económico
    private function usarDiaEconomico(User $empleado, string $fecha): void
    {
        $anio  = now()->year;
        $diaEc = DiaEconomico::where('user_id', $empleado->id)
            ->where('anio', $anio)->first();

        if (!$diaEc || $diaEc->diasPendientes() <= 0) {
            throw new \Exception(
                "El empleado no tiene días económicos disponibles para {$anio}."
            );
        }

        $diaEc->increment('dias_usados');
    }

    private function calcularHoras(string $entrada, string $salida): float
    {
        $inicio = \Carbon\Carbon::createFromFormat('H:i', $entrada);
        $fin    = \Carbon\Carbon::createFromFormat('H:i', $salida);
        if ($fin <= $inicio) return 0;
        return round($inicio->diffInMinutes($fin) / 60, 2);
    }

    private function mapearTipo(string $estado): string
    {
        return match($estado) {
            'horas_extra'          => 'horas_extra',
            'cubriendo_evento'     => 'evento_especial',
            'guardia'              => 'dia_descanso',
            'tiempo_compensatorio' => 'horas_libres',
            default                => 'horas_extra',
        };
    }
}