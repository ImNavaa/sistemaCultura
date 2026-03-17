<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'user_id',
        'fecha',
        'estado',
        'evento_id',
        'hora_entrada',
        'hora_salida',
        'fecha_fin',
        'folio_documento',
        'fecha_compensatorio',
        'inmueble',
        'observaciones',
        'registrado_por',
    ];

    protected $casts = [
        'fecha'               => 'date',
        'fecha_fin'           => 'date',
        'fecha_compensatorio' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public static function etiquetas(): array
    {
        return [
            'a_tiempo'             => ['label' => 'A tiempo',              'color' => 'success',   'icono' => '✅'],
            'tarde'                => ['label' => 'Llegó tarde',           'color' => 'warning',   'icono' => '⏰'],
            'falta_justificada'    => ['label' => 'Falta justificada',     'color' => 'info',      'icono' => '📋'],
            'falta_injustificada'  => ['label' => 'Falta injustificada',   'color' => 'danger',    'icono' => '❌'],
            'cubriendo_evento'     => ['label' => 'Cubriendo evento',      'color' => 'primary',   'icono' => '🎭'],
            'horas_extra'          => ['label' => 'Horas extra',           'color' => 'purple',    'icono' => '⭐'],
            'tiempo_compensatorio' => ['label' => 'Tiempo compensatorio',  'color' => 'secondary', 'icono' => '🔄'],
            'salida_temprana'      => ['label' => 'Salida temprana',       'color' => 'orange',    'icono' => '🚪'],
            'guardia'              => ['label' => 'Guardia',               'color' => 'dark',      'icono' => '🛡️'],
            'vacaciones'           => ['label' => 'Vacaciones',            'color' => 'teal',      'icono' => '🏖️'],
            'dia_economico'        => ['label' => 'Día económico',         'color' => 'indigo',    'icono' => '📅'],
            'incapacidad'          => ['label' => 'Incapacidad',           'color' => 'danger',    'icono' => '🏥'],
            'cita_medica'          => ['label' => 'Cita médica',           'color' => 'info',      'icono' => '👨‍⚕️'],
        ];
    }

    public function etiqueta(): array
    {
        return self::etiquetas()[$this->estado] ?? ['label' => $this->estado, 'color' => 'secondary', 'icono' => '—'];
    }

    // Estados que generan tiempo a favor
    public static function estadosFavor(): array
    {
        return ['horas_extra', 'cubriendo_evento', 'guardia'];
    }

    // Estados que consumen tiempo/días
    public static function estadosCompensacion(): array
    {
        return ['tiempo_compensatorio', 'dia_economico'];
    }
}