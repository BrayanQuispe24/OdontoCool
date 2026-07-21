<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Paciente extends Model
{
    use LogsActivity;
    protected $table = 'pacientes';

    protected $primaryKey = 'codigo_paciente';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'codigo_paciente',
        'contacto_emergencia',
        'telefono_emergencia',
        'persona_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'carnet_identidad');
    }

    public function historialClinico()
    {
        return $this->hasOne(HistorialClinico::class, 'paciente_ci', 'codigo_paciente');
    }
}
