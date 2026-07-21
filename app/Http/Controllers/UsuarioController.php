<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function cambiarPassword(Request $request, string $codigo_usuario)
    {
        try {
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ], [
                'password.required' => 'La nueva contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmación de contraseña no coincide.',
            ]);

            $usuario = User::where('codigo_usuario', $codigo_usuario)->firstOrFail();

            $usuario->update([
                'password' => Hash::make($request->password),
            ]);

            return back()
                ->with('success', 'Contraseña actualizada correctamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al cambiar la contraseña: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function cambiarEmail(Request $request, string $codigo_usuario)
    {
        try {
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            ], [
                'email.required' => 'El nuevo correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser una dirección válida.',
                'email.unique' => 'El correo electrónico ya está en uso.',
            ]);

            $usuario = User::where('codigo_usuario', $codigo_usuario)->firstOrFail();

            $usuario->update([
                'email' => $request->email,
            ]);

            return back()
                ->with('success', 'Correo electrónico actualizado correctamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al cambiar el correo electrónico: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
