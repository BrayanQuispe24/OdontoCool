<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CuotaMulta extends Model
{
    protected $table = 'cuotas_multa';

    protected $primaryKey = 'id_cuota_multa';

    protected $fillable = [
        'monto_multa',
        'fecha_generada',
        'motivo',
        'estado',
        'id_cuota',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function cuotaBoleta()
    {
        return $this->belongsTo(CuotaBoleta::class ,'id_cuota');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    } 
}

