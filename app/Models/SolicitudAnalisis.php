<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SolicitudAnalisis extends Model
{
    protected $table = 'solicitud_analisis';

    protected $primaryKey = 'id';

    protected $fillable = [
        'fecha_solicitud',
        'motivo',
        'estado',
        'analisis_id',
        'tratamiento_id',
        'doctor_ci',
    ];

    public function resultadoAnalisis()
    {
        return $this->hasOne(ResultadoAnalisis::class, 'solicitud_analisis_id');
    }

    public function analisis()
    {
        return $this->belongsTo(Analisis::class, 'analisis_id');
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'tratamiento_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_ci');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', '!=', 'INACTIVO');
    }
}