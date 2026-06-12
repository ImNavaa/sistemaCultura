<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaEvento extends Model
{
    protected $table = 'asistencias_evento';

    protected $fillable = ['inscripcion_id', 'hora_checkin', 'metodo', 'validado_por', 'observaciones'];

    protected $casts = ['hora_checkin' => 'datetime'];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por');
    }
}
