<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\EspacioController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PagoController;

// 1. Usuarios / Clientes
Route::post('/users', [UserController::class, 'store']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

// 2. Vehículos
Route::post('/vehiculos', [VehiculoController::class, 'store']);
Route::get('/vehiculos', [VehiculoController::class, 'index']);
Route::get('/vehiculos/{id}', [VehiculoController::class, 'show']);

// 3. Espacios
Route::post('/espacios', [EspacioController::class, 'store']);
Route::get('/espacios', [EspacioController::class, 'index']);
Route::get('/espacios/{id}', [EspacioController::class, 'show']);
Route::put('/espacios/{id}', [EspacioController::class, 'update']);

// 4. Ingresos
Route::post('/ingresos', [IngresoController::class, 'store']);
Route::get('/ingresos', [IngresoController::class, 'index']);
Route::get('/ingresos/{id}', [IngresoController::class, 'show']);

// 5. Facturas
Route::post('/facturas', [FacturaController::class, 'store']);
Route::get('/facturas', [FacturaController::class, 'index']);
Route::get('/facturas/{id}', [FacturaController::class, 'show']);
Route::get('/ingresos/{ingresoId}/factura', [FacturaController::class, 'facturaPorIngreso']);

// 6. Pagos
Route::post('/pagos', [PagoController::class, 'store']);