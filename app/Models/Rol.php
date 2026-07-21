<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'description',
        'estado',
    ];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'rol_id', 'permiso_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    public function scopeActivo(Builder $query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Obtiene los módulos únicos que pertenecen
     * a los permisos asignados a este rol.
     */
    public function modulos()
    {
        return Modulo::query()
            ->whereHas('permisos.roles', function (Builder $query) {
                $query->where('roles.id', $this->id);
            })
            ->distinct();
    }

    /**
     * Obtiene los módulos con solamente los permisos
     * que este rol tiene dentro de cada módulo.
     */
    public function modulosConPermisos()
    {
        $rolId = $this->id;

        return Modulo::query()
            ->whereHas('permisos.roles', function (Builder $query) use ($rolId) {
                $query->where('roles.id', $rolId);
            })
            ->with([
                'permisos' => function ($query) use ($rolId) {
                    $query->whereHas('roles', function (Builder $query) use ($rolId) {
                        $query->where('roles.id', $rolId);
                    });
                },
            ])
            ->get();
    }
}
