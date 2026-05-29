<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'titulo', 'descripcion', 'estado',
        'fecha_inicio', 'fecha_limite', 'color', 'creador_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_limite' => 'date',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }

    public function miembros()
    {
        return $this->belongsToMany(User::class, 'proyecto_usuario');
    }

    public function tareasCompletadas()
    {
        return $this->hasMany(Tarea::class)->where('estado', 'completada');
    }

    public function progreso(): int
    {
        $total = $this->tareas()->count();
        if ($total === 0) return 0;
        return (int) round(($this->tareasCompletadas()->count() / $total) * 100);
    }

    public static function coloresDisponibles(): array
    {
        return ['#3a7bd5','#7b3ad5','#e8a838','#27ae60','#e74c3c','#16a085','#2c3e50','#e91e8c'];
    }
}
