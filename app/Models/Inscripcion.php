<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';

    protected $fillable = ['folio', 'actividad_id', 'asistente_id', 'estado', 'notas'];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function asistente()
    {
        return $this->belongsTo(Asistente::class);
    }

    public function checkin()
    {
        return $this->hasOne(AsistenciaEvento::class);
    }

    public function asistio(): bool
    {
        return $this->checkin !== null;
    }

    public static function generarFolio(): string
    {
        $anio   = now()->year;
        $ultimo = static::whereYear('created_at', $anio)->orderBy('id', 'desc')->first();
        $num    = $ultimo ? ((int) substr($ultimo->folio, -4)) + 1 : 1;
        return 'REG-' . $anio . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
