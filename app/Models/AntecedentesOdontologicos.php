<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntecedentesOdontologicos extends Model
{
    protected $table = 'antecedentes_odontologicos';

    protected $primaryKey = 'id_antecedente';

    protected $fillable = [
        'observacion_general',
        'fecha_registro',
        'codigo_historial',
    ];


    public function historialClinico()
    {
        return $this->belongsTo(HistorialClinico::class, 'codigo_historial');
    }

    public function detalleAntecedentes()
    {
        return $this->hasMany(DetalleAntecedenteOdontologico::class, 'id_antecedente');
    }
    
}

