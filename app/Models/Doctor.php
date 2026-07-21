<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use LogsActivity;
    protected $table = 'doctores';

    protected $primaryKey = 'codigo_doctor';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codigo_doctor',
        'matricula_profesional',
        'experiencia',
        'telefono_profesional',
        'fecha_contratacion',
        'persona_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'carnet_identidad');
    }

    public function cita()
    {
        return $this->hasOne(Cita::class, 'doctor_ci');
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidad::class, 'asignacion_especialidades', 'doctor_id', 'especialidad_id');
    }

    public function turnos()
    {
        return $this->belongsToMany(Turno::class, 'asignacion_turno_doctor', 'doctor_id', 'turno_id')
            ->withPivot('id', 'dia_semana', 'doctor_id', 'turno_id');
    }

    public function solicitudAnalisis()
    {
        return $this->hasMany(SolicitudAnalisis::class, 'doctor_ci');
    }

}
