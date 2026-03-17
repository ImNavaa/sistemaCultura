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


Route::get('/', function () {
    return redirect()->route('oficios.index');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::get('/usuarios/verificar-email', [UserController::class, 'verificarEmail'])->name('usuarios.verificarEmail');

Route::resource('usuarios', UserController::class);
Route::resource('oficios', OficioController::class);
Route::resource('recibos', ReciboController::class);

Route::get('/calendario', [EventoController::class, 'index'])->name('calendario');
Route::get('/eventos/get', [EventoController::class, 'getEventos'])->name('eventos.get');
Route::post('/eventos', [EventoController::class, 'store'])->name('eventos.store');
Route::delete('/eventos/{evento}', [EventoController::class, 'destroy'])->name('eventos.destroy');
Route::resource('tiempo', TiempoController::class)->only(['index', 'show', 'create', 'store', 'destroy']);

Route::resource('almacen', AlmacenController::class);
Route::resource('entregas', EntregaController::class)->only(['index', 'create', 'store', 'destroy']);
Route::put('/eventos/{evento}', [EventoController::class, 'update'])->name('eventos.update');
Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
Route::post('/asistencias', [AsistenciaController::class, 'store'])->name('asistencias.store');
Route::get('/asistencias/{user}', [AsistenciaController::class, 'show'])->name('asistencias.show');
Route::delete('/asistencias/{asistencia}', [AsistenciaController::class, 'destroy'])->name('asistencias.destroy');
Route::resource('dias-economicos', DiaEconomicoController::class) ->only(['index', 'store', 'update', 'destroy']);
require __DIR__ . '/settings.php';
