<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgoraArea extends Model
{
    protected $table = 'agora_areas';

    protected $fillable = [
        'nombre', 'descripcion', 'color', 'capacidad', 'activa', 'orden',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function scopeActivas($query)
    {
        return $query->where('activa', true)->orderBy('orden')->orderBy('nombre');
    }
}
