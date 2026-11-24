<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanVerifyExit
{
    public function handle(Request $request, Closure $next): Response
    {
        $rolesPermitidos = ['administrador', 'operador_cabina', 'supervisor_salida'];

        if (!$request->user() || !in_array($request->user()->role, $rolesPermitidos)) {
            return response()->json([
                'error' => 'Permisos insuficientes para ejecutar este método'
            ], 403);
        }

        return $next($request);
    }
}