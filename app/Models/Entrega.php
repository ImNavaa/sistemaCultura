<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $table = 'entregas';

    protected $fillable = [
        'articulo_id',
        'cantidad',
        'receptor',
        'fecha_entrega',
        'responsable_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'cantidad'      => 'decimal:2',
    ];

    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}