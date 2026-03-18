<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table = 'recibos';

    protected $fillable = [
        'evento_id',
        'fecha',
        'numero_recibo',
        'nombre_evento',
        'importe',
        'organizador',
        'concepto',
        'foto',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'importe'  => 'decimal:2',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}