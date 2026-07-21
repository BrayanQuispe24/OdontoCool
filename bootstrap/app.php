<?php

use App\Http\Middleware\ContadorVisitaPagina;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\PermisosMiddleware;
use App\Models\Bitacora;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        HandleInertiaRequests::class,
        AddLinkHeadersForPreloadedAssets::class,
        ContadorVisitaPagina::class,
    ]);

    $middleware->alias([
        'permiso' => PermisosMiddleware::class,
    ]);

    $middleware->validateCsrfTokens(except: [
        'boleta-pago/callback/*',
    ]);

    $middleware->trustProxies(
        at: '*',
        headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
            | Request::HEADER_X_FORWARDED_AWS_ELB
    );
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            try {
                // Skip common validation/authentication/404 exceptions to avoid noise
                if ($e instanceof ValidationException ||
                    $e instanceof AuthenticationException ||
                    $e instanceof AuthorizationException ||
                    $e instanceof NotFoundHttpException) {
                    return;
                }

                $user = Auth::user();
                $personaId = $user ? $user->persona_id : null;

                Bitacora::create([
                    'persona_id' => $personaId,
                    'accion' => 'ERROR: '.substr($e->getMessage(), 0, 200),
                    'modulo' => 'SISTEMA',
                    'fecha' => now()->toDateString(),
                    'hora' => now()->toTimeString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'detalles' => [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => substr($e->getTraceAsString(), 0, 2000),
                    ],
                ]);
            } catch (Throwable $ex) {
                // Skip to prevent loops or errors blocking execution
            }
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
