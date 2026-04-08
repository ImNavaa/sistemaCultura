<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear roles y permisos
        $this->call(RolesPermisosSeeder::class);

        // 2. Crear super administrador
        $rol = Rol::where('nombre', 'super_admin')->first();

        User::create([
            'name'         => 'Super Admin',
            'email'        => 'admin@cultura.com',
            'password'     => Hash::make('Admin1234!'),
            'tiene_acceso' => true,
            'rol_id'       => $rol?->id,
        ]);

        $this->command->info('✅ Super admin creado: admin@cultura.com / Admin1234!');
    }
}
