<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Espacio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IngresoController extends Controller
{
    public function index()
    {
        $ingresos = Ingreso::with(['vehiculo.user', 'espacio'])->get();
        return response()->json($ingresos, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'espacio_id' => 'required|exists:espacios,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fechaEntrada' => 'nullable|date'
        ]);

        $espacio = Espacio::findOrFail($request->espacio_id);

        if ($espacio->estado !== 'Disponible') {
            return response()->json([
                'error' => "El espacio {$espacio->codigo} no está disponible (Estado actual: {$espacio->estado})"
            ], 400);
        }

        $fechaEntrada = $request->fechaEntrada ? Carbon::parse($request->fechaEntrada) : Carbon::now();

        $ingreso = Ingreso::create([
            'espacio_id' => $request->espacio_id,
            'vehiculo_id' => $request->vehiculo_id,
            'fechaEntrada' => $fechaEntrada,
            'estado' => 'Activo'
        ]);

        $espacio->estado = 'Ocupado';
        $espacio->save();

        $ingreso->load(['vehiculo.user', 'espacio']);

        return response()->json([
            'mensaje' => 'Ingreso de vehículo registrado correctamente',
            'data' => [
                'ingreso_id' => $ingreso->id,
                'fechaEntrada' => $ingreso->fechaEntrada,
                'estado' => $ingreso->estado,
                'propietario' => [
                    'id' => $ingreso->vehiculo->user->id,
                    'nombre' => $ingreso->vehiculo->user->name,
                    'email' => $ingreso->vehiculo->user->email,
                    'telefono' => $ingreso->vehiculo->user->telefono,
                    'celular' => $ingreso->vehiculo->user->celular,
                ],
                'vehiculo' => [
                    'placa' => $ingreso->vehiculo->placa,
                    'tipo' => $ingreso->vehiculo->tipo,
                ],
                'espacio' => [
                    'codigo' => $ingreso->espacio->codigo,
                    'tipo' => $ingreso->espacio->tipo,
                    'tarifa_por_hora' => $ingreso->espacio->tarifa,
                ]
            ]
        ], 201);
    }

    public function show($id)
    {
        $ingreso = Ingreso::with(['vehiculo.user', 'espacio'])->find($id);

        if (!$ingreso) {
            return response()->json(['error' => 'Registro de ingreso no encontrado'], 404);
        }

        return response()->json($ingreso, 200);
    }
}