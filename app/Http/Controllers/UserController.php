<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Registro opcional de usuario desde Postman (POST /api/register)
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user
        ], 201);
    }

    /**
     * Inicio de sesión en Postman (POST /api/login)
     * Este token servirá tanto en Postman (Bearer) como en SoapUI (<AuthToken>)
     */
    public function login(Request $request)
    {
        // 1. Validar formato y campos obligatorios
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Buscar el usuario y verificar contraseña
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // 3. Emitir el token con Sanctum
        $token = $user->createToken('parqueadero-token');

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'user' => $user
        ], 200);
    }

    /**
     * Consultar usuario autenticado actual (GET /api/me)
     */
    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    /**
     * Cerrar sesión y revocar el token actual (POST /api/logout)
     * Al revocarlo aquí, dejará de funcionar inmediatamente en SoapUI también.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesion cerrada y token revocado exitosamente'
        ], 200);
    }
}