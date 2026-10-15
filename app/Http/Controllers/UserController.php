<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::with('vehiculos')->get(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'celular' => $request->celular,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'mensaje' => 'Cliente registrado correctamente',
            'user' => $user
        ], 201);
    }
}