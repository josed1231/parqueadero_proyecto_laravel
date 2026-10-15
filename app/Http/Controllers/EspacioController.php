<?php

namespace App\Http\Controllers;

use App\Models\Espacio;
use Illuminate\Http\Request;

class EspacioController extends Controller
{
    // Listar todos los espacios (permite filtrar opcionalmente por tipo o estado)
    public function index(Request $request)
    {
        $query = Espacio::query();

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        return response()->json($query->get(), 200);
    }

    // Crear un nuevo espacio en el parqueadero
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:espacios,codigo',
            'tipo' => 'required|in:Carro,Moto',
            'tarifa' => 'required|numeric|min:0',
            'estado' => 'nullable|in:Disponible,Ocupado,Mantenimiento'
        ]);

        $espacio = Espacio::create([
            'codigo' => strtoupper(trim($request->codigo)),
            'tipo' => $request->tipo,
            'tarifa' => $request->tarifa,
            'estado' => $request->estado ?? 'Disponible'
        ]);

        return response()->json([
            'mensaje' => 'Espacio creado correctamente',
            'data' => $espacio
        ], 201);
    }

    // Consultar un espacio específico por ID
    public function show($id)
    {
        $espacio = Espacio::with('ingresos')->find($id);

        if (!$espacio) {
            return response()->json(['error' => 'Espacio no encontrado'], 404);
        }

        return response()->json($espacio, 200);
    }

    // Actualizar tarifa o estado de un espacio
    public function update(Request $request, $id)
    {
        $espacio = Espacio::find($id);

        if (!$espacio) {
            return response()->json(['error' => 'Espacio no encontrado'], 404);
        }

        $request->validate([
            'codigo' => "nullable|string|max:50|unique:espacios,codigo,{$id}",
            'tipo' => 'nullable|in:Carro,Moto',
            'tarifa' => 'nullable|numeric|min:0',
            'estado' => 'nullable|in:Disponible,Ocupado,Mantenimiento'
        ]);

        $espacio->update($request->only(['codigo', 'tipo', 'tarifa', 'estado']));

        return response()->json([
            'mensaje' => 'Espacio actualizado correctamente',
            'data' => $espacio
        ], 200);
    }
}