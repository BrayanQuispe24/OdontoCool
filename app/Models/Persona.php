<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $primaryKey = 'carnet_identidad';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'carnet_identidad',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'direccion',
        'genero',
        'telefono',
    ];

    public function pacientes()
    {
        return $this->hasOne(Paciente::class, 'persona_id', 'carnet_identidad');
    }

    public function doctores()
    {
        return $this->hasOne(Doctor::class, 'persona_id', 'carnet_identidad');
    }

    public function secretarias()
    {
        return $this->hasOne(Secretaria::class, 'persona_id', 'carnet_identidad');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'persona_id', 'carnet_identidad');
    }

    public function propietarios()
    {
        return $this->hasOne(Propietario::class, 'persona_id', 'carnet_identidad');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'persona_id', 'carnet_identidad');
    }
}
