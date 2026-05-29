<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $table = 'tareas';

    protected $fillable = [
        'proyecto_id', 'titulo', 'descripcion', 'estado', 'prioridad',
        'asignado_a', 'creado_por', 'fecha_limite',
        'fecha_completada', 'completado_por', 'orden',
    ];

    protected $casts = [
        'fecha_limite'    => 'date',
        'fecha_completada'=> 'datetime',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function completadoPor()
    {
        return $this->belongsTo(User::class, 'completado_por');
    }

    public static function etiquetasPrioridad(): array
    {
        return [
            'baja'    => ['label' => 'Baja',    'class' => 'bg-secondary'],
            'media'   => ['label' => 'Media',   'class' => 'bg-info'],
            'alta'    => ['label' => 'Alta',    'class' => 'bg-warning text-dark'],
            'urgente' => ['label' => 'Urgente', 'class' => 'bg-danger'],
        ];
    }

    public static function etiquetasEstado(): array
    {
        return [
            'pendiente'   => ['label' => 'Pendiente',   'class' => 'bg-secondary'],
            'en_progreso' => ['label' => 'En Progreso', 'class' => 'bg-primary'],
            'completada'  => ['label' => 'Completada',  'class' => 'bg-success'],
            'cancelada'   => ['label' => 'Cancelada',   'class' => 'bg-dark'],
        ];
    }
}
