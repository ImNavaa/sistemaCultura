<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgoraReserva extends Model
{
    protected $table = 'agora_reservas';

    protected $fillable = [
        'titulo', 'tipo', 'organizador', 'responsable', 'telefono_contacto',
        'fecha', 'hora_inicio', 'hora_fin',
        'areas_ids', 'descripcion', 'notas_internas',
        'estado', 'creado_por',
    ];

    protected $casts = [
        'fecha'     => 'date',
        'areas_ids' => 'array',
    ];

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function areas()
    {
        if (empty($this->areas_ids)) return collect();
        return AgoraArea::whereIn('id', $this->areas_ids)->orderBy('orden')->get();
    }

    public static function coloresTipo(): array
    {
        return [
            'evento'      => '#6366f1',
            'fotografia'  => '#10b981',
            'area'        => '#f59e0b',
        ];
    }

    public static function etiquetasTipo(): array
    {
        return [
            'evento'     => 'Evento',
            'fotografia' => 'Sesión Fotográfica',
            'area'       => 'Área Específica',
        ];
    }

    public static function coloresEstado(): array
    {
        return [
            'confirmado' => null,
            'tentativo'  => 'opacity:.7',
            'cancelado'  => 'opacity:.35; text-decoration:line-through',
        ];
    }

    public function getColorCalendario(): string
    {
        $base = self::coloresTipo()[$this->tipo] ?? '#6366f1';
        if ($this->estado === 'cancelado')  return '#9ca3af';
        if ($this->estado === 'tentativo')  return $base . '99'; // con alfa
        return $base;
    }
}
