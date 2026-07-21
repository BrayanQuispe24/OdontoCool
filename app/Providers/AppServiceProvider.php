<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use App\Models\Bitacora;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Registro de inicio de sesión exitoso
        Event::listen(Login::class, function (Login $event) {
            try {
                $user = $event->user;
                Bitacora::create([
                    'persona_id' => $user->persona_id,
                    'accion' => 'Inicio de sesión exitoso',
                    'modulo' => 'AUTENTICACIÓN',
                    'fecha' => now()->toDateString(),
                    'hora' => now()->toTimeString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'detalles' => [
                        'user_id' => $user->codigo_usuario,
                        'email' => $user->email,
                    ],
                ]);
            } catch (\Exception $e) {
                logger()->error('Error logging login: ' . $e->getMessage());
            }
        });

        // Registro de inicio de sesión fallido
        Event::listen(Failed::class, function (Failed $event) {
            try {
                $email = $event->credentials['email'] ?? 'desconocido';
                $personaId = $event->user ? $event->user->persona_id : null;

                Bitacora::create([
                    'persona_id' => $personaId,
                    'accion' => "Intento de inicio de sesión fallido (Email: {$email})",
                    'modulo' => 'AUTENTICACIÓN',
                    'fecha' => now()->toDateString(),
                    'hora' => now()->toTimeString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'detalles' => [
                        'email_intentado' => $email,
                        'usuario_existe' => !is_null($event->user),
                    ],
                ]);
            } catch (\Exception $e) {
                logger()->error('Error logging failed login: ' . $e->getMessage());
            }
        });

        // Registro de cierre de sesión exitoso
        Event::listen(Logout::class, function (Logout $event) {
            try {
                if ($event->user) {
                    $user = $event->user;
                    Bitacora::create([
                        'persona_id' => $user->persona_id,
                        'accion' => 'Cierre de sesión exitoso',
                        'modulo' => 'AUTENTICACIÓN',
                        'fecha' => now()->toDateString(),
                        'hora' => now()->toTimeString(),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'detalles' => [
                            'user_id' => $user->codigo_usuario,
                            'email' => $user->email,
                        ],
                    ]);
                }
            } catch (\Exception $e) {
                logger()->error('Error logging logout: ' . $e->getMessage());
            }
        });
    }
}
