<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'tipo', 'instructor',
        'fecha_inicio', 'fecha_fin', 'hora_inicio', 'hora_fin',
        'ubicacion', 'modalidad', 'cupo_maximo', 'estado', 'creado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public static function tipos(): array
    {
        return ['evento', 'curso', 'taller', 'conferencia', 'capacitacion'];
    }

    public static function estados(): array
    {
        return ['borrador', 'activo', 'lleno', 'cancelado', 'finalizado'];
    }

    public static function generarCodigo(): string
    {
        $anio   = now()->year;
        $ultimo = static::whereYear('created_at', $anio)->orderBy('id', 'desc')->first();
        $num    = $ultimo ? ((int) substr($ultimo->codigo, -3)) + 1 : 1;
        return 'ACT-' . $anio . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function inscripcionesActivas()
    {
        return $this->hasMany(Inscripcion::class)->where('estado', 'inscrito');
    }

    public function cupoDisponible(): ?int
    {
        if (! $this->cupo_maximo) return null;
        return max(0, $this->cupo_maximo - $this->inscripcionesActivas()->count());
    }
}
