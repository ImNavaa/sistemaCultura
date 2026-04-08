<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';

    protected $fillable = ['modulo', 'accion', 'descripcion'];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permiso');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_permiso')
                    ->withPivot('permitido');
    }
}