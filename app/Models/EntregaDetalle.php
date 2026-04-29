<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaDetalle extends Model
{
    protected $table = 'entrega_detalles';

    protected $fillable = ['entrega_id', 'articulo_id', 'cantidad'];

    protected $casts = ['cantidad' => 'decimal:2'];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }
}
