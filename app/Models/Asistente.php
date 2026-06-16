<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistente extends Model
{
    protected $fillable = [
        'nombre', 'apellidos', 'email', 'telefono', 'edad',
        'genero', 'institucion', 'ocupacion', 'curp', 'ciudad', 'redes_sociales', 'notas',
    ];

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function nombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellidos;
    }

    public function iniciales(): string
    {
        return strtoupper(substr($this->nombre, 0, 1) . substr($this->apellidos, 0, 1));
    }
}
