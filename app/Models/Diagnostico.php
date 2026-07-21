<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnosticos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'sintomas',
        'tipo_diagnostico',
        'gravedad',
        'observaciones',
        'cita_id',
    ];


    public function tratamiento()
    {
        return $this->hasOne(Tratamiento::class, 'diagnostico_id');
    }   

    public function detalleDiagnostico()
    {
        return $this->hasMany(DetalleDiagnostico::class, 'diagnostico_id');
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

}

