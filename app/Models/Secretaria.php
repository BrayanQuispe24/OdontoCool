<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Secretaria extends Model
{
    use LogsActivity;
    protected $table = 'secretarias';

    protected $primaryKey = 'codigo_secretaria';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codigo_secretaria',
        'fecha_contratacion',
        'persona_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'carnet_identidad');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'secretaria_ci');
    }

    public function turnos()
    {
        return $this->belongsToMany(Turno::class, 'asignacion_turno_secretaria', 'secretaria_id', 'turno_id')
            ->withPivot('id', 'dia_semana', 'secretaria_id', 'turno_id');
    }


    public function boletaPago()
    {
        return $this->hasMany(BoletaPago::class, 'secretaria_ci');
    }
}
