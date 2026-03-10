<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'telefono'      => 'nullable|string|max:20',
            'cargo'         => 'nullable|string|max:100',
            'horario'       => 'nullable|string|max:100',
            'dias_laborales'=> 'nullable|string|max:255',
        ]);

        User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'telefono'       => $request->telefono,
            'cargo'          => $request->cargo,
            'horario'        => $request->horario,
            'dias_laborales' => $request->dias_laborales,
        ]);

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
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $usuario->id,
            'password'       => 'nullable|string|min:8|confirmed',
            'telefono'       => 'nullable|string|max:20',
            'cargo'          => 'nullable|string|max:100',
            'horario'        => 'nullable|string|max:100',
            'dias_laborales' => 'nullable|string|max:255',
        ]);

        $datos = [
            'name'           => $request->name,
            'email'          => $request->email,
            'telefono'       => $request->telefono,
            'cargo'          => $request->cargo,
            'horario'        => $request->horario,
            'dias_laborales' => $request->dias_laborales,
        ];

        // Solo actualiza password si se ingresó uno nuevo
        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}