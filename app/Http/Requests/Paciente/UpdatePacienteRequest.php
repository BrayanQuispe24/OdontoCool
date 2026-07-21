<?php

namespace App\Http\Requests\Paciente;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePacienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         // un administrador puede crear otro administrador, pero un doctor o secretaria no puede crear un administrador
        // $user = $this->user();
        // if ($user && ($user->tieneRol('Administrador') || $user->tieneRol('Doctor') || $user->tieneRol('Secretaria'))) {
        //     return true;
        // }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             // 'codigo_usuario' => ['required', 'string', 'max:255'],
            'email' => ['sometimes','required', 'string', 'email', 'max:255'],
            'url' => ['sometimes','nullable', 'string', 'max:255'],
            'foto_perfil' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
           // 'password' => ['sometimes','required', 'string', 'min:8', 'confirmed'],
            'estado' => ['sometimes','required', 'string', 'max:20', Rule::in(['activo', 'inactivo'])],
            // 'rol_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            // persona
            'carnet_identidad' => ['sometimes','required', 'string', 'max:10'],
            'nombre' => ['sometimes','required', 'string', 'max:255'],
            'apellido' => ['sometimes','required', 'string', 'max:255'],
            'fecha_nacimiento' => ['sometimes','required', 'date'],
            'direccion' => ['sometimes','nullable', 'string', 'max:255'],
            'genero' => ['sometimes','required', 'string', 'max:15'],
            'telefono' => ['sometimes','required', 'string', 'max:20'],
            // paciente
            'contacto_emergencia' => ['sometimes','required', 'string', 'max:20'],
            'telefono_emergencia' => ['sometimes','required', 'string', 'max:20'],
        ];
    }
}
