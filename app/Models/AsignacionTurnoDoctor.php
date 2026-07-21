<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AsignacionTurnoDoctor extends Pivot
{
    protected $table = 'asignacion_turnos';

    protected $fillable = [
        'id',
        'dia_semana',
        'doctor_id',
        'turno_id',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'codigo_doctor');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id', 'id');
    }
}
