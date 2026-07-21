<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDiagnostico extends Model
{
    protected $table = 'detalle_diagnostico';

    protected $primaryKey = 'id';

    protected $fillable = [
        'observacion',
        'zona_bucal',
        'diagnostico_id',
        'diente_id',
    ];

    public function diagnostico(){
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    public function diente(){
        return $this->belongsTo(Diente::class, 'diente_id');
    }

}

