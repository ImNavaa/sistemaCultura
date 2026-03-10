<?php

namespace App\Services;

use App\Models\User;
use App\Models\RegistroTiempo;
use App\Models\SaldoTiempo;

class TiempoService
{
    // Recalcula y actualiza el saldo de un empleado
    public function recalcularSaldo(User $empleado): SaldoTiempo
    {
        $horasFavor = RegistroTiempo::where('user_id', $empleado->id)
            ->where('categoria', 'favor')
            ->sum('horas');

        $horasCompensadas = RegistroTiempo::where('user_id', $empleado->id)
            ->where('categoria', 'compensacion')
            ->sum('horas');

        $saldo = $horasFavor - $horasCompensadas;

        $saldoTiempo = SaldoTiempo::updateOrCreate(
            ['user_id' => $empleado->id],
            [
                'horas_favor'          => $horasFavor,
                'horas_compensadas'    => $horasCompensadas,
                'saldo'                => $saldo,
                'ultima_actualizacion' => now(),
            ]
        );

        return $saldoTiempo;
    }

    // Registra horas a favor
    public function registrarHorasFavor(User $empleado, array $datos): RegistroTiempo
    {
        $registro = RegistroTiempo::create([
            'user_id'     => $empleado->id,
            'fecha'       => $datos['fecha'],
            'tipo'        => $datos['tipo'],
            'categoria'   => 'favor',
            'horas'       => $datos['horas'],
            'descripcion' => $datos['descripcion'] ?? null,
        ]);

        $this->recalcularSaldo($empleado);

        return $registro;
    }

    // Registra compensación de horas
    public function registrarCompensacion(User $empleado, array $datos): RegistroTiempo
    {
        // Verifica que tenga saldo suficiente
        $saldo = SaldoTiempo::where('user_id', $empleado->id)->first();
        
        if (!$saldo || $saldo->saldo < $datos['horas']) {
            throw new \Exception("El empleado no tiene suficiente saldo de horas. Saldo actual: " . ($saldo->saldo ?? 0) . " horas.");
        }

        $registro = RegistroTiempo::create([
            'user_id'     => $empleado->id,
            'fecha'       => $datos['fecha'],
            'tipo'        => $datos['tipo'],
            'categoria'   => 'compensacion',
            'horas'       => $datos['horas'],
            'descripcion' => $datos['descripcion'] ?? null,
        ]);

        $this->recalcularSaldo($empleado);

        return $registro;
    }

    // Convierte horas decimales a formato legible (ej: 1.5 → "1h 30min")
    public static function formatearHoras(float $horas): string
    {
        $h = (int) floor($horas);
        $min = (int) round(($horas - $h) * 60);

        if ($h > 0 && $min > 0) return "{$h}h {$min}min";
        if ($h > 0) return "{$h}h";
        return "{$min}min";
    }
}