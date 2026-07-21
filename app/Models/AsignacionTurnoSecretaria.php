<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AsignacionTurnoSecretaria extends Pivot
{
    protected $table = 'asignacion_turnos';

    protected $fillable = [
        'id',
        'dia_semana',
        'secretaria_id',
        'turno_id',
    ];

    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_id', 'id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id', 'id');
    }
}
