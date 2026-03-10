<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oficio extends Model
{
    protected $table = 'oficios';

    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'nombre_evento',
        'numero_oficio',
        'cobrado',
        'monto_cobrado',
        'organizador',
        'foto',
    ];

    protected $casts = [
        'fecha'         => 'date',
        'cobrado'       => 'boolean',
        'monto_cobrado' => 'decimal:2',
    ];
}
