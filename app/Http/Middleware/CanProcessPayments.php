<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanProcessPayments
{
    public function handle(Request $request, Closure $next): Response
    {
        $rolesPermitidos = ['administrador', 'cajero'];

        if (!$request->user() || !in_array($request->user()->role, $rolesPermitidos)) {
            return response()->json([
                'error' => 'Permisos insuficientes para ejecutar este método'
            ], 403);
        }

        return $next($request);
    }
}