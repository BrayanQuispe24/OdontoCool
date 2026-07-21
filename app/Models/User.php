<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $table = 'users';

    protected $primaryKey = 'codigo_usuario';

    protected $keyType = 'string';

    public $incrementing = false; // como ya no es un entero, se desactiva el autoincremento

    protected $fillable = [
        'codigo_usuario',
        'email',
        'password',
        'estado',
        'url',
        'rol_id',
        'persona_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'carnet_identidad');
    }

    public function tieneRol(string $rolNombre)
    {
        return $this->rol && $this->rol->nombre === $rolNombre;
    }

    public function obtenerPermisos()
    {
        return $this->rol ? $this->rol->obtenerPermisosPorRol() : collect();
    }

    public function tienePermiso(string $permisoNombre): bool
    {
        if (! $this->rol) {
            return false;
        }

        return $this->rol
            ->permisos()
            ->where('permisos.nombre', $permisoNombre)
            ->where('permisos.estado', 'activo')
            ->exists();
    }

    public function preferencia()
    {
        return $this->hasOne(UsuarioPreferencia::class, 'usuario_id', 'codigo_usuario');
    }
}
