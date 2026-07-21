<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AsignacionPrecio extends Model
{
    protected $table = 'asignacion_precios';

    protected $primaryKey = 'id';

    protected $fillable = [
        'servicio_id',
        'precio_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function precio()
    {
        return $this->belongsTo(Precio::class, 'precio_id');
    }

    public function scopeVigente(Builder $query)
    {
        return $query->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now());
    }
}