<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use SoapFault;

trait ValidaTokenSoap
{
    /**
     * Extrae el token del Header HTTP o del XML <soap:Header><AuthToken> de SoapUI
     * y lo valida contra la tabla personal_access_tokens de Sanctum.
     */
    protected function validarTokenDesdeSoap(Request $request): User
    {
        $token = null;

        // 1. Intentar obtenerlo si se envió como Header HTTP Authorization: Bearer
        if ($request->bearerToken()) {
            $token = $request->bearerToken();
        }

        // 2. Si no viene por Header HTTP, extraerlo del XML <soap:Header> (SoapUI)
        if (! $token) {
            $xmlContent = $request->getContent();
            if (! empty($xmlContent)) {
                // Soporta <AuthToken>TOKEN</AuthToken> o <token>TOKEN</token>
                if (preg_match('/<(?:[\w]+:)?(?:AuthToken|token)\b[^>]*>(.*?)<\/(?:[\w]+:)?(?:AuthToken|token)>/s', $xmlContent, $matches)) {
                    $token = trim($matches[1]);
                }
            }
        }

        // Limpiar prefijo "Bearer " si el usuario lo pegó completo en el XML
        if ($token) {
            $token = trim(str_replace('Bearer ', '', $token));
        }

        if (! $token) {
            throw new SoapFault('Client', 'No autenticado: Debe enviar el <AuthToken> en el <soap:Header>.');
        }

        // 3. Buscar el token en la tabla personal_access_tokens de Laravel Sanctum
        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken || ! ($accessToken->tokenable instanceof User)) {
            throw new SoapFault('Client', 'Token invalido, expirado o sesion cerrada en Postman.');
        }

        // Retornar el usuario dueño del token
        return $accessToken->tokenable;
    }
}