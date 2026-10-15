<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingresos';

    protected $fillable = [
        'espacio_id',
        'vehiculo_id',
        'fechaEntrada',
        'fechaSalida',
        'estado'
    ];

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class, 'ingreso_id');
    }
}