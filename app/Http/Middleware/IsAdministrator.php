<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'administrador') {
            return response()->json([
                'error' => 'Permisos insuficientes para ejecutar este método'
            ], 403);
        }

        return $next($request);
    }
}