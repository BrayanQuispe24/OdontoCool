<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPreferencia extends Model
{
    protected $table = 'usuario_preferencias';
    protected $primaryKey = 'id';
    protected $fillable = [
        'theme',
        'font_size',
        'usuario_id',
    ];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'codigo_usuario');
    }
}
