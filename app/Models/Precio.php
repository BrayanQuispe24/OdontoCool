<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $table = 'precios';

    protected $primaryKey = 'id';

    protected $fillable = [
        'moneda',
        'monto',
        'estado'
    ];

    public function asignaciones()
    {
        return $this->hasMany(AsignacionPrecio::class, 'precio_id');
    }
}