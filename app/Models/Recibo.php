<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table = 'recibos';

    protected $fillable = [
        'fecha',
        'nombre_evento',
        'numero_recibo',
        'importe',
        'organizador',
        'concepto',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'importe'  => 'decimal:2',
    ];
}