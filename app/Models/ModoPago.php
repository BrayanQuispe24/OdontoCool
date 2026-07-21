<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class ModoPago extends Model
{
    protected $table = 'modo_pago';

    protected $primaryKey = 'id_modo_pago';

    protected $fillable = [
        'nombre',
        'estado',
    ];
    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function boletaPago()
    {
        return $this->hasMany(BoletaPago::class, 'id_modo_pago');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    } 
}

