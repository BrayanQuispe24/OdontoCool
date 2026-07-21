<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Analisis extends Model
{
    protected $table = 'analisis';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function solicitudAnalisis()
    {
        return $this->hasMany(SolicitudAnalisis::class, 'analisis_id');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}

