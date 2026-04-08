<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $conAcceso    = User::where('tiene_acceso', true)->orderBy('name')->get();
        $sinAcceso    = User::where('tiene_acceso', false)->orderBy('name')->get();
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
        if ($request->tiene_acceso) {
            $request->validate([
                'name'           => 'required|string|max:255',
                'email'          => 'required|email|unique:users,email',
                'password'       => 'required|string|min:8|confirmed',
                'telefono'       => 'nullable|string|max:20',
                'cargo'          => 'nullable|string|max:100',
                'horario'        => 'nullable|string|max:100',
                'dias_laborales' => 'nullable|string|max:255',
            ]);

            User::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'password'       => Hash::make($request->password),
                'telefono'       => $request->telefono,
                'cargo'          => $request->cargo,
                'horario'        => $request->horario,
                'dias_laborales' => $request->dias_laborales,
                'tiene_acceso'   => true,
            ]);
        } else {
            $request->validate([
                'name'           => 'required|string|max:255',
                'telefono'       => 'nullable|string|max:20',
                'cargo'          => 'nullable|string|max:100',
                'horario'        => 'nullable|string|max:100',
                'dias_laborales' => 'nullable|string|max:255',
            ]);

            User::create([
                'name'           => $request->name,
                'telefono'       => $request->telefono,
                'cargo'          => $request->cargo,
                'horario'        => $request->horario,
                'dias_laborales' => $request->dias_laborales,
                'tiene_acceso'   => false,
            ]);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function show(User $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        if ($usuario->tiene_acceso) {
            $request->validate([
                'name'           => 'required|string|max:255',
                'email'          => 'required|email|unique:users,email,' . $usuario->id,
                'password'       => 'nullable|string|min:8|confirmed',
                'telefono'       => 'nullable|string|max:20',
                'cargo'          => 'nullable|string|max:100',
                'horario'        => 'nullable|string|max:100',
                'dias_laborales' => 'nullable|string|max:255',
            ]);
        } else {
            $request->validate([
                'name'           => 'required|string|max:255',
                'telefono'       => 'nullable|string|max:20',
                'cargo'          => 'nullable|string|max:100',
                'horario'        => 'nullable|string|max:100',
                'dias_laborales' => 'nullable|string|max:255',
            ]);
        }

        $datos = [
            'name'           => $request->name,
            'telefono'       => $request->telefono,
            'cargo'          => $request->cargo,
            'horario'        => $request->horario,
            'dias_laborales' => $request->dias_laborales,
        ];

        if ($usuario->tiene_acceso) {
            $datos['email'] = $request->email;
            if ($request->filled('password')) {
                $datos['password'] = Hash::make($request->password);
            }
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
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
}
