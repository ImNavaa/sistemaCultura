<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'fecha',
        'nombre_evento',
        'organizador',
        'tipo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];
}