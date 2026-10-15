<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'ingreso_id',
        'fechaEmision',
        'total',
        'estado'
    ];

    public function ingreso()
    {
        return $this->belongsTo(Ingreso::class, 'ingreso_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'factura_id');
    }
}