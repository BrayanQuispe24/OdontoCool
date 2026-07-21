<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turnos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'estado',
        'hora_inicio',
        'hora_fin',
    ];

    public function doctores()
    {
        return $this->belongsToMany(Doctor::class, 'asignacion_turnos', 'turno_id', 'doctor_id')
            ->withPivot('id', 'dia_semana', 'doctor_id', 'turno_id');
    }

    public function secretarias()
    {
        return $this->belongsToMany(Secretaria::class, 'asignacion_turno_secretaria', 'turno_id', 'secretaria_id')
            ->withPivot('id', 'dia_semana', 'secretaria_id', 'turno_id');
    }
}
