<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'cargo',
        'fecha_nacimiento',
        'recinto',
        'horario',
        'dias_laborales',
        'tiene_acceso',
        'rol_id',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'fecha_nacimiento'  => 'date',
        ];
    }

    public static function recintos(): array
    {
        return ['Teatro', 'Ágora', 'Oficinas Administrativas', 'Biblioteca', 'Museo', 'Otro'];
    }

    public function proximoCumpleanos(): ?\Carbon\Carbon
    {
        if (!$this->fecha_nacimiento) return null;
        $hoy    = now()->startOfDay();
        $cumple = \Carbon\Carbon::create(null, $this->fecha_nacimiento->month, $this->fecha_nacimiento->day)->startOfDay();
        if ($cumple->lt($hoy)) $cumple->addYear();
        return $cumple;
    }

    public function diasParaCumpleanos(): ?int
    {
        $cumple = $this->proximoCumpleanos();
        if (!$cumple) return null;
        return (int) now()->startOfDay()->diffInDays($cumple, false);
    }

    public function edad(): ?int
    {
        return $this->fecha_nacimiento?->age;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    // Agrega estos métodos al modelo User existente

    public function registrosTiempo()
    {
        return $this->hasMany(RegistroTiempo::class, 'user_id');
    }

    public function saldoTiempo()
    {
        return $this->hasOne(SaldoTiempo::class, 'user_id');
    }
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'user_id');
    }

    public function asistenciaHoy()
    {
        return $this->hasOne(Asistencia::class, 'user_id')
            ->whereDate('fecha', today());
    }
    public function diasEconomicos()
    {
        return $this->hasMany(DiaEconomico::class, 'user_id');
    }

    public function diasEconomicosAnio(int $anio = null)
    {
        return $this->hasOne(DiaEconomico::class, 'user_id')
            ->where('anio', $anio ?? now()->year);
    }

    public function vacaciones()
    {
        return $this->hasMany(Vacacion::class, 'user_id');
    }

    public function diasPendientes()
    {
        return $this->hasMany(DiaPendiente::class, 'user_id');
    }

    public function diasPendientesPendientes()
    {
        return $this->hasMany(DiaPendiente::class, 'user_id')->where('estado', 'pendiente');
    }
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function permisosExtra()
    {
        return $this->belongsToMany(Permiso::class, 'user_permiso')
            ->withPivot('permitido');
    }

    // Verificar si tiene un permiso
    public function puede(string $modulo, string $accion): bool
    {
        // Super admin siempre puede todo
        if ($this->rol?->nombre === 'super_admin') return true;

        // Verificar permiso extra del usuario
        $permisoExtra = $this->permisosExtra
            ->where('modulo', $modulo)
            ->where('accion', $accion)
            ->first();

        if ($permisoExtra) {
            return (bool) $permisoExtra->pivot->permitido;
        }

        // Verificar permiso del rol
        return $this->rol?->permisos
            ->where('modulo', $modulo)
            ->where('accion', $accion)
            ->isNotEmpty() ?? false;
    }
}
