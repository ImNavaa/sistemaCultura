<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'fecha',
        'numero_recibo',
        'nombre_evento',
        'organizador',
        'autoriza',
        'tipo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];
}