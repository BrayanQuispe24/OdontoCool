<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AsignacionEstadoCita extends Model
{
    protected $table = 'asignacion_estado_cita';

    protected $fillable = [
        'cita_id',
        'estado_cita_id',
        'observacion',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    public function estadoCita()
    {
        return $this->belongsTo(EstadoCita::class, 'estado_cita_id');
    }   
   
}
