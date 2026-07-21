<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacoras';

    protected $fillable = [
        'persona_id',
        'accion',
        'modulo',
        'fecha',
        'hora',
        'ip_address',
        'user_agent',
        'detalles',
    ];

    protected $casts = [
        'detalles' => 'array',
        'fecha' => 'date',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'carnet_identidad');
    }
}
