<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $table = 'entregas';

    protected $fillable = [
        'folio',
        'receptor',
        'unidad_solicitante',
        'fecha_entrega',
        'responsable_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    public static function generarFolio(): string
    {
        $anio   = now()->format('Y');
        $ultimo = static::whereYear('created_at', $anio)->max('id') ?? 0;
        return 'VSA-' . $anio . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    public function detalles()
    {
        return $this->hasMany(EntregaDetalle::class, 'entrega_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
