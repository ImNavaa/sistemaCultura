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
        'horario',
        'dias_laborales',
        'tiene_acceso',
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
            'password' => 'hashed',
        ];
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
}
