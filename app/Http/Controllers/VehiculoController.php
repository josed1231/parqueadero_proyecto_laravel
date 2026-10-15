<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        return response()->json(Vehiculo::with('user')->get(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'placa' => 'required|string|max:20|unique:vehiculos,placa',
            'tipo' => 'required|in:Carro,Moto'
        ]);

        $vehiculo = Vehiculo::create([
            'user_id' => $request->user_id,
            'placa' => strtoupper(trim($request->placa)),
            'tipo' => $request->tipo
        ]);

        $vehiculo->load('user');

        return response()->json([
            'mensaje' => 'Vehículo registrado correctamente',
            'data' => [
                'id' => $vehiculo->id,
                'placa' => $vehiculo->placa,
                'tipo' => $vehiculo->tipo,
                'propietario' => [
                    'id' => $vehiculo->user->id,
                    'nombre' => $vehiculo->user->name,
                    'email' => $vehiculo->user->email,
                    'telefono' => $vehiculo->user->telefono,
                    'celular' => $vehiculo->user->celular,
                ]
            ]
        ], 201);
    }

    public function show($id)
    {
        $vehiculo = Vehiculo::with(['user', 'ingresos'])->find($id);

        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        return response()->json($vehiculo, 200);
    }
}