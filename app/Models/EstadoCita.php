<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCita extends Model
{
    protected $table = 'estado_cita';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function asignacionesEstadoCita()
    {
        return $this->hasMany(AsignacionEstadoCita::class, 'estado_cita_id');
    }
}
