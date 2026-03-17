<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaEconomico extends Model
{
    protected $table = 'dias_economicos';

    protected $fillable = [
        'user_id',
        'anio',
        'dias_asignados',
        'dias_usados',
        'observaciones',
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function diasPendientes(): int
    {
        return $this->dias_asignados - $this->dias_usados;
    }
}