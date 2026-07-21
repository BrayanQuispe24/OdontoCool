<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContadorPagina extends Model
{
    protected $table = 'contador_pagina';

    protected $fillable = [
        'nombre_pagina',
        'contador',
    ];
}
