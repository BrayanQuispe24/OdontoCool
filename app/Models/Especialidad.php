<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidades';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    public function doctores()
    {
        return $this->belongsToMany(Doctor::class, 'asignacion_especialidades', 'especialidad_id', 'doctor_id');
    }
}
