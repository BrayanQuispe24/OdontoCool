<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Diente extends Model
{
    protected $table = 'dientes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'numero',
        'tipo',
        'ubicacion',
        'estado',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function tratamientoDiente()
    {
        return $this->hasMany(TratamientoDiente::class, 'diente_id');
    }   

    public function detalleDiagnostico()
    {
        return $this->hasMany(DetalleDiagnostico::class, 'diente_id');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    } 
}

