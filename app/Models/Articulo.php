<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table = 'articulos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'cantidad_actual',
        'unidad',
        'responsable_id',
    ];

    protected $casts = [
        'cantidad_actual' => 'decimal:2',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'articulo_id');
    }

    // Verifica si hay suficiente stock
    public function tieneSuficiente(float $cantidad): bool
    {
        return $this->cantidad_actual >= $cantidad;
    }
}