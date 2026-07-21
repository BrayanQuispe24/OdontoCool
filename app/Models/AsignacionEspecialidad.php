<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AsignacionEspecialidad extends Pivot
{
    protected $table = 'asignacion_especialidades';

    protected $primaryKey = 'id';

    protected $fillable = [
        'doctor_id',
        'especialidad_id',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'codigo_doctor');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id', 'id');
    }
}
