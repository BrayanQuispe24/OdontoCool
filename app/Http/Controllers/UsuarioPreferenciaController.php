<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioPreferenciaController extends Controller
{

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['nullable', 'string', 'in:light,dark,odontocool,infantil,juvenil,adulto'],
            'font_size' => ['nullable', 'string', 'in:small,normal,large,extra-large'],
        ]);

        $user = $request->user();

        $preferencia = $user->preferencia()->firstOrCreate([
            'usuario_id' => $user->codigo_usuario,
        ]);

        if (array_key_exists('theme', $validated)) {
            $preferencia->theme = $validated['theme'];
        }

        if (array_key_exists('font_size', $validated)) {
            $preferencia->font_size = $validated['font_size'];
        }

        $preferencia->save();

        return back();
    }


    public function get(Request $request)
    {
        $user = $request->user();

        $preferencia = $user->preferencia()->first();

        return response()->json([
            'theme' => $preferencia?->theme ?? 'light',
            'font_size' => $preferencia?->font_size ?? 'normal',
        ]);
    }
}
