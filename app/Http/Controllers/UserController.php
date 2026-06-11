<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $esSuperAdmin = auth()->user()->rol?->nombre === 'super_admin';

        $base = $esSuperAdmin
            ? User::query()
            : User::whereDoesntHave('rol', fn($q) => $q->where('nombre', 'super_admin'));

        $conAcceso = (clone $base)->where('tiene_acceso', true)->orderBy('name')->get();
        $sinAcceso = (clone $base)->where('tiene_acceso', false)->orderBy('name')->get();

        return view('usuarios.index', compact('conAcceso', 'sinAcceso'));
    }

    public function create()
    {
        // Decide qué formulario mostrar
        $tipo = request('tipo', 'sin_acceso');
        return view('usuarios.create', compact('tipo'));
    }

    public function store(Request $request)
    {
        $camposComunes = [
            'name'             => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:20',
            'cargo'            => 'nullable|string|max:100',
            'horario'          => 'nullable|string|max:100',
            'dias_laborales'   => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'recinto'          => 'nullable|string|max:100',
        ];

        if ($request->tiene_acceso) {
            $request->validate(array_merge($camposComunes, [
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]));

            User::create([
                'name'             => $request->name,
                'email'            => $request->email,
                'password'         => Hash::make($request->password),
                'telefono'         => $request->telefono,
                'cargo'            => $request->cargo,
                'horario'          => $request->horario,
                'dias_laborales'   => $request->dias_laborales,
                'fecha_nacimiento' => $request->fecha_nacimiento ?: null,
                'recinto'          => $request->recinto ?: null,
                'tiene_acceso'     => true,
            ]);
        } else {
            $request->validate($camposComunes);

            User::create([
                'name'             => $request->name,
                'telefono'         => $request->telefono,
                'cargo'            => $request->cargo,
                'horario'          => $request->horario,
                'dias_laborales'   => $request->dias_laborales,
                'fecha_nacimiento' => $request->fecha_nacimiento ?: null,
                'recinto'          => $request->recinto ?: null,
                'tiene_acceso'     => false,
            ]);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function show(User $usuario)
    {
        $this->denegarAccesoSuperAdmin($usuario);
        $anio           = now()->year;
        $saldo          = $usuario->saldoTiempo;
        $diasEcon       = $usuario->diasEconomicosAnio($anio)->first();
        $vacaciones     = $usuario->vacaciones()->where('anio', $anio)->first();
        $diasPendientes = $usuario->diasPendientes()->orderBy('fecha_generacion', 'desc')->get();
        $ultimosRegistros = $usuario->registrosTiempo()->orderBy('fecha', 'desc')->take(8)->get();
        return view('usuarios.show', compact('usuario', 'anio', 'saldo', 'diasEcon', 'vacaciones', 'diasPendientes', 'ultimosRegistros'));
    }

    public function edit(User $usuario)
    {
        $this->denegarAccesoSuperAdmin($usuario);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $this->denegarAccesoSuperAdmin($usuario);
        $nuevoAcceso = $request->boolean('tiene_acceso');
        $teniaAcceso = $usuario->tiene_acceso;

        // No se puede quitar acceso al propio usuario ni al super_admin
        if ($teniaAcceso && !$nuevoAcceso) {
            if ($usuario->id === auth()->id()) {
                return back()->withErrors(['error' => 'No puedes quitarte el acceso a ti mismo.'])->withInput();
            }
            if ($usuario->rol?->nombre === 'super_admin') {
                return back()->withErrors(['error' => 'No se puede quitar el acceso al super administrador.'])->withInput();
            }
        }

        $reglas = [
            'name'             => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:20',
            'cargo'            => 'nullable|string|max:100',
            'horario'          => 'nullable|string|max:100',
            'dias_laborales'   => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'recinto'          => 'nullable|string|max:100',
        ];

        if ($nuevoAcceso) {
            $reglas['email']    = 'required|email|unique:users,email,' . $usuario->id;
            $reglas['password'] = $teniaAcceso
                ? 'nullable|string|min:8|confirmed'
                : 'required|string|min:8|confirmed';
        }

        $request->validate($reglas);

        $datos = [
            'name'             => $request->name,
            'telefono'         => $request->telefono,
            'cargo'            => $request->cargo,
            'horario'          => $request->horario,
            'dias_laborales'   => $request->dias_laborales,
            'fecha_nacimiento' => $request->fecha_nacimiento ?: null,
            'recinto'          => $request->recinto ?: null,
            'tiene_acceso'     => $nuevoAcceso,
        ];

        if ($nuevoAcceso) {
            $datos['email'] = $request->email;
            if ($request->filled('password')) {
                $datos['password'] = Hash::make($request->password);
            }
        }

        $usuario->update($datos);

        $msg = $nuevoAcceso && !$teniaAcceso
            ? 'Acceso al sistema otorgado correctamente.'
            : (!$nuevoAcceso && $teniaAcceso
                ? 'Acceso al sistema revocado.'
                : 'Empleado actualizado correctamente.');

        return redirect()->route('usuarios.index')->with('success', $msg);
    }

    public function destroy(User $usuario)
    {
        $this->denegarAccesoSuperAdmin($usuario);
        try {
            $usuario->delete();
            return redirect()->route('usuarios.index')
                ->with('success', 'Empleado eliminado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            $mensaje = 'No se puede eliminar a "' . $usuario->name . '" porque tiene registros relacionados en el sistema.';

            // Identificar qué tabla tiene la restricción
            if (str_contains($e->getMessage(), 'entregas')) {
                $mensaje .= ' Tiene entregas de almacén registradas a su nombre.';
            } elseif (str_contains($e->getMessage(), 'asistencias')) {
                $mensaje .= ' Tiene registros de asistencia vinculados.';
            } elseif (str_contains($e->getMessage(), 'registros_tiempo')) {
                $mensaje .= ' Tiene registros de tiempo vinculados.';
            } else {
                $mensaje .= ' Primero elimina o reasigna los registros relacionados antes de eliminar al empleado.';
            }

            return redirect()->route('usuarios.index')
                ->with('error', $mensaje);
        }
    }

    // Verificar email duplicado en tiempo real
    public function verificarEmail(Request $request)
    {
        $existe = User::where('email', $request->email)
            ->where('id', '!=', $request->id ?? 0)
            ->exists();
        return response()->json(['existe' => $existe]);
    }

    private function denegarAccesoSuperAdmin(User $usuario): void
    {
        if (
            $usuario->rol?->nombre === 'super_admin' &&
            auth()->user()->rol?->nombre !== 'super_admin'
        ) {
            abort(403);
        }
    }
}
