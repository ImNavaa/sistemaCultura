<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroTiempo extends Model
{
    protected $table = 'registros_tiempo';

    protected $fillable = [
        'user_id',
        'fecha',
        'tipo',
        'categoria',
        'horas',
        'descripcion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'horas' => 'decimal:2',
    ];

    // Relación con el empleado
    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope para registros a favor
    public function scopeFavor($query)
    {
        return $query->where('categoria', 'favor');
    }

    // Scope para compensaciones
    public function scopeCompensacion($query)
    {
        return $query->where('categoria', 'compensacion');
    }
}