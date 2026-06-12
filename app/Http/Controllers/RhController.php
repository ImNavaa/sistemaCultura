<?php

namespace App\Http\Controllers;

use App\Models\DiaEconomico;
use App\Models\DiaPendiente;
use App\Models\SaldoTiempo;
use App\Models\User;
use App\Models\Vacacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RhController extends Controller
{
    public function dashboard()
    {
        $anio = now()->year;

        $totalPersonal = User::visibles()->count();

        // Cumpleaños del mes
        $cumpleMes = User::visibles()->whereNotNull('fecha_nacimiento')
            ->whereMonth('fecha_nacimiento', now()->month)
            ->orderByRaw('DAY(fecha_nacimiento)')
            ->get();

        // Horas pendientes totales (saldo > 0)
        $horasPendientes = SaldoTiempo::where('saldo', '>', 0)->sum('saldo');

        // Días económicos disponibles este año
        $diasEconDisp = DiaEconomico::where('anio', $anio)
            ->selectRaw('SUM(dias_asignados - dias_usados) as total')
            ->value('total') ?? 0;

        // Vacaciones disponibles este año
        $vacDisponibles = Vacacion::where('anio', $anio)
            ->selectRaw('SUM(dias_asignados - dias_usados) as total')
            ->value('total') ?? 0;

        // Días pendientes sin usar (global)
        $diasPendientesCount = DiaPendiente::where('estado', 'pendiente')->count();

        // Próximos cumpleaños (30 días)
        $hoy    = now()->startOfDay();
        $proximos = User::visibles()->whereNotNull('fecha_nacimiento')
            ->orderBy('name')
            ->get()
            ->map(function ($u) use ($hoy) {
                $cumple = Carbon::create(null, $u->fecha_nacimiento->month, $u->fecha_nacimiento->day)->startOfDay();
                if ($cumple->lt($hoy)) $cumple->addYear();
                $u->dias_para_cumple      = (int) $hoy->diffInDays($cumple, false);
                $u->fecha_cumple_proxima  = $cumple;
                return $u;
            })
            ->filter(fn($u) => $u->dias_para_cumple >= 0 && $u->dias_para_cumple <= 30)
            ->sortBy('dias_para_cumple')
            ->values();

        // Empleados con sus datos RH del año actual
        $empleados = User::visibles()->with([
            'saldoTiempo',
            'diasEconomicosAnio',
            'diasPendientesPendientes',
            'vacaciones' => fn($q) => $q->where('anio', $anio),
        ])->orderBy('name')->get()
          ->map(function ($u) use ($hoy) {
              if ($u->fecha_nacimiento) {
                  $cumple = Carbon::create(null, $u->fecha_nacimiento->month, $u->fecha_nacimiento->day)->startOfDay();
                  if ($cumple->lt($hoy)) $cumple->addYear();
                  $u->dias_para_cumple = (int) $hoy->diffInDays($cumple, false);
              } else {
                  $u->dias_para_cumple = null;
              }
              return $u;
          });

        // Personal agrupado por recinto
        $porRecinto = $empleados->groupBy(fn($u) => $u->recinto ?? 'Sin recinto');

        return view('rh.dashboard', compact(
            'totalPersonal', 'cumpleMes', 'horasPendientes',
            'diasEconDisp', 'vacDisponibles', 'diasPendientesCount',
            'proximos', 'empleados', 'porRecinto', 'anio'
        ));
    }
}
