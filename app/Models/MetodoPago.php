<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodo_pago';

    protected $primaryKey = 'id_metodo_pago';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function CuotaBoleta(){
        return $this->hasMany(CuotaBoleta::class, 'id_metodo_pago');
    }
}

