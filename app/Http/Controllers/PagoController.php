<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Factura;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'factura_id' => 'required|exists:facturas,id',
            'monto' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string'
        ]);

        $factura = Factura::findOrFail($request->factura_id);

        $pago = Pago::create([
            'factura_id' => $request->factura_id,
            'fechaPago' => Carbon::now(),
            'monto' => $request->monto,
            'metodoPago' => $request->metodo_pago
        ]);

        // Marcar factura como Pagada
        $factura->estado = 'Pagado';
        $factura->save();

        return response()->json([
            'mensaje' => 'Pago registrado correctamente',
            'data' => [
                'pago_id' => $pago->id,
                'factura_id' => $pago->factura_id,
                'monto' => $pago->monto,
                'metodo_pago' => $pago->metodoPago,
                'fecha_pago' => $pago->fechaPago,
                'estado_factura' => $factura->estado
            ]
        ], 201);
    }
}