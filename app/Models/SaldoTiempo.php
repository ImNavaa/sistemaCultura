<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoTiempo extends Model
{
    protected $table = 'saldos_tiempo';

    protected $fillable = [
        'user_id',
        'horas_favor',
        'horas_compensadas',
        'saldo',
        'ultima_actualizacion',
    ];

    protected $casts = [
        'horas_favor'        => 'decimal:2',
        'horas_compensadas'  => 'decimal:2',
        'saldo'              => 'decimal:2',
        'ultima_actualizacion' => 'datetime',
    ];

    // Relación con el empleado
    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}