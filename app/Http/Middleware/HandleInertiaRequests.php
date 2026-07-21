<?php

namespace App\Http\Middleware;

use App\Models\ContadorPagina;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /** @var User|null $user */
        $user = $request->user();
        // esto agrege para el contador
        if ($user) {
            $user->load([
                'preferencia',
                'rol',
                'persona',
            ]);
        }
        $paginaActual = '/'.trim($request->path(), '/');

        if ($paginaActual === '/') {
            $paginaActual = '/';
        }

        $contadorPaginaActual = ContadorPagina::where('nombre_pagina', $paginaActual)
            ->value('contador') ?? 0;

        // ---------
        $rol = $user?->rol;
        $persona = $user?->persona;

        return [
            ...parent::share($request),
            //esto agrege para el contador
            'contadorPaginaActual' => $contadorPaginaActual,
            'paginaActual' => $paginaActual,
            // ---------
            'auth' => [
                'user' => $user
                    ? [
                        'id' => $user->codigo_usuario ?? $user->usuario_id ?? $user->id,
                        'codigo_usuario' => $user->codigo_usuario ?? null,
                        'persona_id' => $user->persona_id ?? null,

                        'name' => $persona
                            ? trim($persona->nombre.' '.$persona->apellido)
                            : 'Usuario',

                        'nombre' => $persona?->nombre,
                        'apellido' => $persona?->apellido,

                        'nombre_completo' => $persona
                            ? trim($persona->nombre.' '.$persona->apellido)
                            : 'Usuario',

                        'email' => $user->email,
                        'url' => $user->url ?? null,

                        'rol' => $rol
                            ? [
                                'id' => $rol->id,
                                'nombre' => $rol->nombre,
                                'description' => $rol->description,
                                'estado' => $rol->estado,
                            ]
                            : null,

                        'preferencias' => [
                            'theme' => $user->preferencia?->theme ?? 'light',
                            'font_size' => $user->preferencia?->font_size ?? 'normal',
                        ],
                    ]
                    : null,

                'modulos' => $rol
                    ? $rol->modulosConPermisos()
                    : [],

                'permisos' => $rol
                    ? $rol->permisos()->pluck('nombre')->values()
                    : [],
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'flash_id' => fn () => $request->session()->get('flash_id'),
                'qr_base64' => fn () => $request->session()->get('qr_base64'),
                'qr_cuota_id' => fn () => $request->session()->get('qr_cuota_id'),
                'estado' => fn () => $request->session()->get('estado'),
            ],
        ];
    }
}
