<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TratamientoDiente extends Model
{
    protected $table = 'tratamientos_dientes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'cara_dental',
        'observacion',
        'fecha_registro',
        'tratamiento_planificado',
        'estado',
        'diente_id',
        'tratamiento_id',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'tratamiento_id');
    }

    public function diente()
    {
        return $this->belongsTo(Diente::class, 'diente_id');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}

