<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espacio extends Model
{
    use HasFactory;

    protected $table = 'espacios';

    protected $fillable = [
        'codigo',
        'tipo',    // 'Carro' o 'Moto'
        'tarifa',  // Tarifa por hora según el tipo
        'estado'   // 'Disponible', 'Ocupado', 'Mantenimiento'
    ];

    // Relación: Un espacio puede registrar múltiples ingresos a lo largo del tiempo
    public function ingresos()
    {
        return $this->hasMany(Ingreso::class);
    }
}