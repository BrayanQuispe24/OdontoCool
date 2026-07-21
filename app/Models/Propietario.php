<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    use LogsActivity;
    protected $table = 'propietarios';
    protected $primaryKey = 'codigo_propietario';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo_propietario',
        'fecha_inicio',
        'porcentaje_participacion',
        'persona_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'carnet_identidad');
    }
}
