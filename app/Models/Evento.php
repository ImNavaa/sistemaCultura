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
        'autoriza',
        'tipo',
        'hora_inicio',
        'hora_fin',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function oficio()
    {
        return $this->hasOne(Oficio::class);
    }

    public function recibo()
    {
        return $this->hasOne(Recibo::class);
    }
}