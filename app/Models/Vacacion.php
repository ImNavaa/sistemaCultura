<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{
    protected $table = 'vacaciones';

    protected $fillable = ['user_id', 'anio', 'dias_asignados', 'dias_usados', 'observaciones'];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function diasDisponibles(): int
    {
        return max(0, $this->dias_asignados - $this->dias_usados);
    }
}
