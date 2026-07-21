<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\LogsActivity;

class HistorialClinico extends Model
{
    use LogsActivity;

    protected $table = 'historiales_clinicos';

    protected $primaryKey = 'codigo_historial';

    protected $fillable = [
        'alergias',
        'antecedentes_medicos',
        'enfermedades_base',
        'motivo_apertura',
        'fecha_apertura',
        'fecha_actualizacion',
        'observaciones_generales',
        'estado',
        'paciente_ci',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class, 'codigo_historial');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci');
    }

    public function antecedentesOdontologicos()
    {
        return $this->hasOne(AntecedentesOdontologicos::class, 'codigo_historial');
    }

    public function tratamiento()
    {
        return $this->hasMany(Tratamiento::class, 'codigo_historial');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}

