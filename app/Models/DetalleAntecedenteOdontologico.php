<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleAntecedenteOdontologico extends Model
{
    protected $table = 'detalle_antecedentes_odontologicos';

    protected $primaryKey = 'id_detalle_antecedente';

    protected $fillable = [
        'nombre_tratamiento',
        'descripcion',
        'fecha_tratamiento',
        'lugar_tratamiento',
        'observacion',
        'id_antecedente',
    ];

    public function antecedenteOdontologico(){
        return $this->belongsTo(AntecedentesOdontologicos::class, 'id_antecedente');
    }

}

