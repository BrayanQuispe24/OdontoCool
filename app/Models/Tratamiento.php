<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\LogsActivity;

class Tratamiento extends Model
{
    use LogsActivity;

    protected $table = 'tratamientos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'objetivo_tratamiento',
        'observacion',
        'estado',
        'fecha_inicio',
        'fecha_fin_estimada',
        'fecha_fin_real',
        'diagnostico_id',
        'codigo_historial',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function tratamientoDiente()
    {
        return $this->hasOne(TratamientoDiente::class, 'tratamiento_id');
    }

    public function tratamientosDientes()
    {
        return $this->hasMany(TratamientoDiente::class, 'tratamiento_id');
    }

    public function historialClinico()
    {
        return $this->belongsTo(HistorialClinico::class, 'codigo_historial');
    }

    public function recetaRecomendaciones()
    {
        return $this->hasOne(RecetaRecomendacion::class, 'tratamiento_id');
    }

    public function solicitudAnalisis()
    {
        return $this->hasMany(SolicitudAnalisis::class, 'tratamiento_id');
    }

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    public function servicioPrestado()
    {
        return $this->hasMany(ServicioPrestado::class, 'tratamiento_id');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}

