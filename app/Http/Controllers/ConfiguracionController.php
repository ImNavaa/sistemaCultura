<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return view('configuracion.index');
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual'      => 'required|string',
            'password'             => 'required|string|min:8|confirmed',
            'password_confirmation'=> 'required|string',
        ], [
            'password_actual.required'       => 'Ingresa tu contraseña actual.',
            'password.required'              => 'Ingresa la nueva contraseña.',
            'password.min'                   => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'             => 'Las contraseñas nuevas no coinciden.',
            'password_confirmation.required' => 'Confirma la nueva contraseña.',
        ]);

        $usuario = auth()->user();

        if (!Hash::check($request->password_actual, $usuario->password)) {
            return back()
                ->withErrors(['password_actual' => 'La contraseña actual es incorrecta.'])
                ->withInput()
                ->with('seccion', 'password');
        }

        $usuario->update(['password' => Hash::make($request->password)]);

        return redirect()->route('configuracion')
            ->with('success', 'Contraseña actualizada correctamente.');
    }
}
