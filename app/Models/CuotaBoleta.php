<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\LogsActivity;

class CuotaBoleta extends Model
{
    use LogsActivity;

    protected $table = 'cuota_boleta';

    protected $primaryKey = 'id_cuota';

    protected $fillable = [
        'numero_cuota',
        'monto_cuota',
        'fecha_vencimiento',
        'fecha_pago',
        'observacion',
        'estado',
        'id_transaccion',
        'comprobante',
        'id_metodo_pago',
        'id_boleta',
    ];

    protected $attributes = [
        'estado' => 'PENDIENTE',
    ];

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago');
    }

    public function boletaPago()
    {
        return $this->belongsTo(BoletaPago::class, 'id_boleta');
    }

    public function cuotaMulta()
    {
        return $this->hasOne(CuotaMulta::class, 'id_cuota');
    }

    public function scopePendiente(Builder $query)
    {
        return $query->where('estado', 'PENDIENTE');
    } 
}

