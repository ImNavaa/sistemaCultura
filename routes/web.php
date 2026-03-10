<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OficioController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\TiempoController;

Route::get('/', function () {
    return redirect()->route('oficios.index');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('usuarios', UserController::class);
Route::resource('oficios', OficioController::class);
Route::resource('recibos', ReciboController::class);

Route::get('/calendario', [EventoController::class, 'index'])->name('calendario');
Route::get('/eventos/get', [EventoController::class, 'getEventos'])->name('eventos.get');
Route::post('/eventos', [EventoController::class, 'store'])->name('eventos.store');
Route::delete('/eventos/{evento}', [EventoController::class, 'destroy'])->name('eventos.destroy');
Route::resource('tiempo', TiempoController::class)->only(['index', 'show', 'create', 'store', 'destroy']);


require __DIR__.'/settings.php';