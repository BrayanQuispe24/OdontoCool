<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RecetaRecomendacion extends Model
{
    protected $table = 'recetas_recomendacion';

    protected $primaryKey = 'id';

    protected $fillable = [
        'fecha_emision',
        'observacion_general',
        'tratamiento_id',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleRecomendacion::class, 'receta_recomendacion_id');
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'tratamiento_id');
    }
}