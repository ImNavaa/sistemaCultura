<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaPendiente extends Model
{
    protected $table = 'dias_pendientes';

    protected $fillable = [
        'user_id', 'fecha_generacion', 'motivo',
        'estado', 'fecha_uso', 'registrado_por',
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
        'fecha_uso'        => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }
}
