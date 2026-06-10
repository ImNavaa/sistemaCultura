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
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\HerramientasController;
use App\Http\Controllers\AgoraController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\TareaController;

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

// ── CONFIGURACIÓN ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
    Route::post('/configuracion/password', [ConfiguracionController::class, 'cambiarPassword'])->name('configuracion.password');
});

// ── HERRAMIENTAS ──────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/herramientas/img-pdf', [HerramientasController::class, 'imgPdf'])->name('herramientas.img-pdf');
});

// ── CALENDARIO ──────────────────────────────────────────
Route::middleware(['auth', 'permiso:calendario,ver'])->group(function () {
    Route::get('/calendario', [EventoController::class, 'index'])->name('calendario');
    Route::get('/eventos/get', [EventoController::class, 'getEventos'])->name('eventos.get');
    Route::get('/calendario/reporte', [EventoController::class, 'reporte'])->name('calendario.reporte');
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

// ── ÁGORA ─────────────────────────────────────────────────
Route::middleware(['auth', 'permiso:agora,ver'])->group(function () {
    Route::get('/agora', [AgoraController::class, 'index'])->name('agora.index');
    Route::get('/agora/reservas', [AgoraController::class, 'getReservas'])->name('agora.reservas.get');
    Route::get('/agora/reporte', [AgoraController::class, 'reporte'])->name('agora.reporte');

    Route::middleware('permiso:agora,crear')->group(function () {
        Route::post('/agora/reservas', [AgoraController::class, 'store'])->name('agora.reservas.store');
    });
    Route::middleware('permiso:agora,editar')->group(function () {
        Route::put('/agora/reservas/{reserva}', [AgoraController::class, 'update'])->name('agora.reservas.update');
        Route::patch('/agora/reservas/{reserva}/mover', [AgoraController::class, 'moverFecha'])->name('agora.reservas.mover');
        // Gestión de áreas (solo admin)
        Route::get('/agora/areas', [AgoraController::class, 'areasIndex'])->name('agora.areas');
        Route::post('/agora/areas', [AgoraController::class, 'areasStore'])->name('agora.areas.store');
        Route::put('/agora/areas/{area}', [AgoraController::class, 'areasUpdate'])->name('agora.areas.update');
    });
    Route::middleware('permiso:agora,eliminar')->group(function () {
        Route::delete('/agora/reservas/{reserva}', [AgoraController::class, 'destroy'])->name('agora.reservas.destroy');
        Route::delete('/agora/areas/{area}', [AgoraController::class, 'areasDestroy'])->name('agora.areas.destroy');
    });
});

// ── PROYECTOS Y TAREAS ────────────────────────────────────
Route::middleware(['auth', 'permiso:proyectos,ver'])->group(function () {
    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');

    Route::middleware('permiso:proyectos,crear')->group(function () {
        Route::get('/proyectos/create', [ProyectoController::class, 'create'])->name('proyectos.create');
        Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
        Route::post('/proyectos/{proyecto}/tareas', [TareaController::class, 'store'])->name('tareas.store');
    });

    Route::middleware('permiso:proyectos,editar')->group(function () {
        Route::get('/proyectos/{proyecto}/edit', [ProyectoController::class, 'edit'])->name('proyectos.edit');
        Route::put('/proyectos/{proyecto}', [ProyectoController::class, 'update'])->name('proyectos.update');
        Route::put('/tareas/{tarea}', [TareaController::class, 'update'])->name('tareas.update');
    });

    Route::middleware('permiso:proyectos,eliminar')->group(function () {
        Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');
        Route::delete('/tareas/{tarea}', [TareaController::class, 'destroy'])->name('tareas.destroy');
    });

    Route::get('/proyectos/{proyecto}', [ProyectoController::class, 'show'])->name('proyectos.show');

    // Cambio de estado accesible para admin O el usuario asignado (validado en el controller)
    Route::patch('/tareas/{tarea}/estado', [TareaController::class, 'updateEstado'])->name('tareas.estado');
});

// ── PERMISOS ──────────────────────────────────────────────
Route::middleware(['auth', 'permiso:usuarios,editar'])->group(function () {
    Route::get('/permisos/{usuario}', [PermisoController::class, 'index'])->name('permisos.index');
    Route::put('/permisos/{usuario}', [PermisoController::class, 'update'])->name('permisos.update');
});

require __DIR__ . '/settings.php';