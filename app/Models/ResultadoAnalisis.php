<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ResultadoAnalisis extends Model
{
    protected $table = 'resultado_analisis';

    protected $primaryKey = 'id';

    protected $fillable = [
        'fecha_resultado',
        'resultado',
        'observaciones',
        'interpretacion',
        'archivo_resultado',
        'estado',
        'solicitud_analisis_id',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function solicitudAnalisis()
    {
        return $this->belongsTo(SolicitudAnalisis::class, 'solicitud_analisis_id');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', '!=', 'INACTIVO');
    }
}

