<?php

namespace App\Http\Middleware;

use App\Models\ContadorPagina;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContadorVisitaPagina
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldCountVisit($request)) {
            $page = '/' . trim($request->path(), '/');

            if ($page === '/') {
                $page = '/';
            }

            $visit = ContadorPagina::firstOrCreate(
                ['nombre_pagina' => $page],
                ['contador' => 0]
            );

            $visit->increment('contador');
        }

        return $next($request);
    }
    private function shouldCountVisit(Request $request): bool
    {
        return $request->isMethod('GET')
            && !$request->expectsJson()
            && !$request->is('login')
            && !$request->is('logout')
            && !$request->is('register')
            && !$request->is('storage/*')
            && !$request->is('build/*')
            && !$request->is('api/*')
            && !$request->is('_debugbar/*');
    }
}
