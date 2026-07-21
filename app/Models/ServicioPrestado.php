<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ServicioPrestado extends Model
{
    protected $table = 'servicio_prestados';

    protected $primaryKey = 'id';

    protected $fillable = [
        'cantidad',
        'precio',
        'descuento',
        'subtotal',
        'fecha_servicio',
        'observacion',
        'estado',
        'tratamiento_id',
        'servicio_id',
        'id_boleta',
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'tratamiento_id');
    }
    
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function boletaPago()
    {
        return $this->belongsTo(BoletaPago::class, 'id_boleta');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'ACTIVO');
    }  
}

