<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'user_id',
        'placa',
        'tipo' // 'Carro' o 'Moto'
    ];

    // Relación con el Cliente
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con Ingresos
    public function ingresos()
    {
        return $this->hasMany(Ingreso::class);
    }
}