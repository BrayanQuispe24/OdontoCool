<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\LogsActivity;

class BoletaPago extends Model
{
    use LogsActivity;

    protected $table = 'boleta_pago';

    protected $primaryKey = 'id_boleta';

    protected $fillable = [
        'descuento',
        'fecha_emision',
        'total',
        'estado_pago',
        'id_modo_pago',
        'paciente_ci',
        'secretaria_ci',
    ];

    protected $attributes = [
        'estado_pago' => 'PENDIENTE',
    ];

    public function modoPago()
    {
        return $this->belongsTo(ModoPago::class, 'id_modo_pago');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci');
    }

    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_ci');
    }
    
    public function cuotaBoleta()
    {
        return $this->hasMany(CuotaBoleta::class, 'id_boleta');
    }

    public function servicioPrestado()
    {
        return $this->hasMany(ServicioPrestado::class,'id_boleta');
    }

    public function scopePendiente(Builder $query)
    {
        return $query->where('estado_pago', 'PENDIENTE');
    } 
}

