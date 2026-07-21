<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Cita extends Model
{
    use LogsActivity;

    protected $table = 'citas';

    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'fecha_cita',
        'hora_inicio',
        'hora_fin',
        'motivo',
        'observacion',
        'fecha_registro',
        'paciente_ci',
        'codigo_historial',
        'secretaria_ci',
        'doctor_ci',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci');
    }

    public function historialClinico()
    {
        return $this->belongsTo(HistorialClinico::class, 'codigo_historial');
    }

    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_ci');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_ci');
    }

    public function diagnostico()
    {
        return $this->hasOne(Diagnostico::class, 'cita_id');
    }

    public function asignacionesEstadoCita()
    {
        return $this->hasMany(AsignacionEstadoCita::class, 'cita_id');
    }

    public function ultimoEstadoAsignado()
    {
        return $this->hasOne(AsignacionEstadoCita::class, 'cita_id', 'id_cita')
            ->latestOfMany();
    }
}
