<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'estado',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function asignacionesPrecio()
    {
        return $this->hasMany(AsignacionPrecio::class, 'servicio_id');
    }

    public function servicioPrestado()
    {
        return $this->hasMany(ServicioPrestado::class, 'servicio_id');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}

