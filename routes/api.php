<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\EspacioController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\SoapController;

// 1. RUTAS PÚBLICAS
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

// RUTA SOAP (valida el token y el rol internamente en SoapController)
Route::any('/soap', [SoapController::class, 'server']);

// 2. RUTAS REST PROTEGIDAS CON BEARER TOKEN (Sanctum -> 401 si no hay token válido)
Route::middleware('auth:sanctum')->group(function () {

    // Sesión actual
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/logout', [UserController::class, 'logout']);

    // --- MÓDULO DE FACTURACIÓN Y CONTROL DE SALIDA ---

    // consultarFactura: permitido para los 4 perfiles
    Route::get('/facturas/{id}', [FacturaController::class, 'show']);

    // generarFactura: administrador, operador_cabina
    Route::post('/facturas', [FacturaController::class, 'store'])
        ->middleware('can.generate.invoices');

    // listarFacturas y registrarPagoSalida: administrador, cajero
    Route::middleware('can.process.payments')->group(function () {
        Route::get('/facturas', [FacturaController::class, 'index']);
        Route::post('/pagos', [PagoController::class, 'store']);
    });

    // consultarFacturaPorIngreso: administrador, operador_cabina, supervisor_salida
    Route::get('/ingresos/{ingresoId}/factura', [FacturaController::class, 'facturaPorIngreso'])
        ->middleware('can.verify.exit');

    // --- ADMINISTRACIÓN GENERAL (Solo administrador) ---
    Route::middleware('is.admin')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);

        Route::post('/espacios', [EspacioController::class, 'store']);
        Route::put('/espacios/{id}', [EspacioController::class, 'update']);
    });

    // Consultas y operación base del parqueadero
    Route::get('/espacios', [EspacioController::class, 'index']);
    Route::get('/espacios/{id}', [EspacioController::class, 'show']);

    Route::post('/vehiculos', [VehiculoController::class, 'store']);
    Route::get('/vehiculos', [VehiculoController::class, 'index']);
    Route::get('/vehiculos/{id}', [VehiculoController::class, 'show']);

    Route::post('/ingresos', [IngresoController::class, 'store']);
    Route::get('/ingresos', [IngresoController::class, 'index']);
    Route::get('/ingresos/{id}', [IngresoController::class, 'show']);
});