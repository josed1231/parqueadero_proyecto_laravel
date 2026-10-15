<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WsSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener el token de seguridad desde el Header HTTP
        $securityToken = $request->header('X-WS-Security-Token') ?? $request->header('Authorization');

        // Validar si se envió el token
        if (!$securityToken) {
            return response()->json([
                'error' => 'Acceso Denegado: Header WS-Security-Token faltante.',
                'code' => 401
            ], 401);
        }

        // Limpiar prefijo 'Bearer ' si se envía mediante Authorization Header
        $token = str_replace('Bearer ', '', $securityToken);

        // Definir token maestro/esperado (o consultar en DB)
        $validToken = env('WS_SECURITY_TOKEN', 'token_seguridad_parqueadero_64bits_secret_key_2026_cotecnova_app');

        // Validar coincidencia del Token de Seguridad
        if ($token !== $validToken) {
            return response()->json([
                'error' => 'Acceso Denegado: Token WS-Security inválido o expirado.',
                'code' => 403
            ], 403);
        }

        return $next($request);
    }
}