<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['ingreso.vehiculo.user', 'ingreso.espacio', 'pagos'])->get();
        return response()->json($facturas, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ingreso_id' => 'required|exists:ingresos,id',
        ]);

        $ingreso = Ingreso::with(['espacio', 'vehiculo.user'])->findOrFail($request->ingreso_id);

        if ($ingreso->estado === 'Finalizado') {
            return response()->json([
                'error' => 'Este ingreso ya ha sido finalizado y facturado previamente.'
            ], 400);
        }

        $fechaEntrada = Carbon::parse($ingreso->fechaEntrada);
        $fechaSalida = Carbon::now();

        // Calcular las horas cobradas (mínimo 1 hora)
        $horas = ceil($fechaEntrada->diffInMinutes($fechaSalida) / 60);
        if ($horas < 1) {
            $horas = 1;
        }

        $tarifaPorHora = $ingreso->espacio->tarifa;
        $total = $horas * $tarifaPorHora;

        // Crear la factura usando los campos exactos de tu tabla
        $factura = Factura::create([
            'ingreso_id' => $ingreso->id,
            'fechaEmision' => $fechaSalida,
            'total' => $total,
            'estado' => 'Pendiente'
        ]);

        // Finalizar el ingreso y liberar el espacio
        $ingreso->estado = 'Finalizado';
        $ingreso->fechaSalida = $fechaSalida;
        $ingreso->save();

        $ingreso->espacio->estado = 'Disponible';
        $ingreso->espacio->save();

        return response()->json([
            'mensaje' => 'Factura generada y espacio liberado correctamente',
            'data' => [
                'factura_id' => $factura->id,
                'fechaEmision' => $factura->fechaEmision,
                'horas_cobradas' => $horas,
                'tarifa_por_hora' => $tarifaPorHora,
                'total_a_pagar' => $factura->total,
                'estado_factura' => $factura->estado,
                'cliente' => $ingreso->vehiculo->user->name,
                'placa_vehiculo' => $ingreso->vehiculo->placa,
                'espacio_liberado' => $ingreso->espacio->codigo
            ]
        ], 201);
    }

    public function show($id)
    {
        $factura = Factura::with(['ingreso.vehiculo.user', 'ingreso.espacio', 'pagos'])->find($id);

        if (!$factura) {
            return response()->json(['error' => 'Factura no encontrada'], 404);
        }

        return response()->json($factura, 200);
    }
}