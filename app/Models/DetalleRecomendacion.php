<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DetalleRecomendacion extends Model
{
    protected $table = 'detalle_recomendacion';

    protected $primaryKey = 'id';

    protected $fillable = [
        'descripcion',
        'dosis',
        'duracion',
        'frecuencia',
        'receta_recomendacion_id',
    ];

    public function recetaRecomendacion()
    {
        return $this->belongsTo(RecetaRecomendacion::class, 'receta_recomendacion_id');
    }
}