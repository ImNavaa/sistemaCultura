<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next, string $modulo, string $accion = 'ver')
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $usuario->loadMissing(['rol.permisos', 'permisosExtra']);

        if (!$usuario->puede($modulo, $accion)) {
            abort(403);
        }

        return $next($request);
    }
}
