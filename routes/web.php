<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OficioController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\TiempoController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\DiaEconomicoController;
use App\Http\Controllers\DashboardController;

// ── SETUP INICIAL (eliminar después del primer uso) ───────
Route::get('/setup-inicial-xyz123', function () {
    // Crear roles y permisos
    \Artisan::call('db:seed', ['--class' => 'RolesPermisosSeeder', '--force' => true]);

    // Crear super admin si no existe
    $rol   = \App\Models\Rol::where('nombre', 'super_admin')->first();
    $admin = \App\Models\User::firstOrCreate(
        ['email' => 'admin@cultura.com'],
        [
            'name'         => 'Super Admin',
            'password'     => \Illuminate\Support\Facades\Hash::make('Admin1234!'),
            'tiene_acceso' => true,
            'rol_id'       => $rol?->id,
        ]
    );

    return $admin->wasRecentlyCreated
        ? '✅ Admin creado correctamente. Entra con admin@cultura.com / Admin1234!'
        : 'ℹ️ El admin ya existía.';
});

// ── REDIRECCIONES ─────────────────────────────────────────
Route::get('/', fn() => redirect()->route('inicio'));
Route::get('/home', fn() => redirect()->route('inicio'))->name('home');
Route::get('/dashboard', fn() => redirect()->route('inicio'))->name('dashboard');

// ── INICIO / DASHBOARD ────────────────────────────────────
Route::middleware('auth')->get('/inicio', [DashboardController::class, 'index'])->name('inicio');

// ── CALENDARIO ──────────────────────────────────────────
Route::middleware(['auth', 'permiso:calendario,ver'])->group(function () {
    Route::get('/calendario', [EventoController::class, 'index'])->name('calendario');
    Route::get('/eventos/get', [EventoController::class, 'getEventos'])->name('eventos.get');
    Route::middleware('permiso:calendario,crear')->post('/eventos', [EventoController::class, 'store'])->name('eventos.store');
    Route::middleware('permiso:calendario,editar')->put('/eventos/{evento}', [EventoController::class, 'update'])->name('eventos.update');
    Route::middleware('permiso:calendario,eliminar')->delete('/eventos/{evento}', [EventoController::class, 'destroy'])->name('eventos.destroy');
});

// ── OFICIOS ──────────────────────────────────────────────
Route::middleware(['auth', 'permiso:oficios,ver'])->group(function () {
    Route::get('/oficios', [OficioController::class, 'index'])->name('oficios.index');
    Route::get('/oficios/{oficio}', [OficioController::class, 'show'])->name('oficios.show');
    Route::middleware('permiso:oficios,crear')->group(function () {
        Route::get('/oficios/create', [OficioController::class, 'create'])->name('oficios.create');
        Route::post('/oficios', [OficioController::class, 'store'])->name('oficios.store');
    });
    Route::middleware('permiso:oficios,editar')->group(function () {
        Route::get('/oficios/{oficio}/edit', [OficioController::class, 'edit'])->name('oficios.edit');
        Route::put('/oficios/{oficio}', [OficioController::class, 'update'])->name('oficios.update');
    });
    Route::middleware('permiso:oficios,eliminar')->delete('/oficios/{oficio}', [OficioController::class, 'destroy'])->name('oficios.destroy');
});

// ── RECIBOS ──────────────────────────────────────────────
Route::middleware(['auth', 'permiso:recibos,ver'])->group(function () {
    Route::get('/recibos', [ReciboController::class, 'index'])->name('recibos.index');
    Route::get('/recibos/{recibo}', [ReciboController::class, 'show'])->name('recibos.show');
    Route::middleware('permiso:recibos,crear')->group(function () {
        Route::get('/recibos/create', [ReciboController::class, 'create'])->name('recibos.create');
        Route::post('/recibos', [ReciboController::class, 'store'])->name('recibos.store');
    });
    Route::middleware('permiso:recibos,editar')->group(function () {
        Route::get('/recibos/{recibo}/edit', [ReciboController::class, 'edit'])->name('recibos.edit');
        Route::put('/recibos/{recibo}', [ReciboController::class, 'update'])->name('recibos.update');
    });
    Route::middleware('permiso:recibos,eliminar')->delete('/recibos/{recibo}', [ReciboController::class, 'destroy'])->name('recibos.destroy');
});

// ── ASISTENCIAS ───────────────────────────────────────────
Route::middleware(['auth', 'permiso:asistencias,ver'])->group(function () {
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::get('/asistencias/{user}', [AsistenciaController::class, 'show'])->name('asistencias.show');
    Route::middleware('permiso:asistencias,crear')->post('/asistencias', [AsistenciaController::class, 'store'])->name('asistencias.store');
    Route::middleware('permiso:asistencias,eliminar')->delete('/asistencias/{asistencia}', [AsistenciaController::class, 'destroy'])->name('asistencias.destroy');
});

// ── USUARIOS ──────────────────────────────────────────────
Route::middleware(['auth', 'permiso:usuarios,ver'])->group(function () {
    Route::get('/usuarios/verificar-email', [UserController::class, 'verificarEmail'])->name('usuarios.verificarEmail');
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    // Las rutas estáticas (create) deben ir ANTES que las dinámicas ({usuario})
    Route::middleware('permiso:usuarios,crear')->group(function () {
        Route::get('/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    });
    Route::get('/usuarios/{usuario}', [UserController::class, 'show'])->name('usuarios.show');
    Route::middleware('permiso:usuarios,editar')->group(function () {
        Route::get('/usuarios/{usuario}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
    });
    Route::middleware('permiso:usuarios,eliminar')->delete('/usuarios/{usuario}', [UserController::class, 'destroy'])->name('usuarios.destroy');
});

// ── ALMACÉN ───────────────────────────────────────────────
Route::middleware(['auth', 'permiso:almacen,ver'])->group(function () {
    Route::resource('almacen', AlmacenController::class);
    Route::resource('entregas', EntregaController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/entregas/{entrega}/pdf', [EntregaController::class, 'pdf'])->name('entregas.pdf');
});

// ── CONTROL DE TIEMPO ─────────────────────────────────────
Route::middleware(['auth', 'permiso:tiempo,ver'])->group(function () {
    Route::resource('tiempo', TiempoController::class)->only(['index', 'show', 'create', 'store', 'destroy']);
});

// ── DÍAS ECONÓMICOS ───────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::resource('dias-economicos', DiaEconomicoController::class)->only(['index', 'store', 'update', 'destroy']);
});

// ── PERMISOS ──────────────────────────────────────────────
Route::middleware(['auth', 'permiso:usuarios,editar'])->group(function () {
    Route::get('/permisos/{usuario}', [PermisoController::class, 'index'])->name('permisos.index');
    Route::put('/permisos/{usuario}', [PermisoController::class, 'update'])->name('permisos.update');
});

require __DIR__ . '/settings.php';