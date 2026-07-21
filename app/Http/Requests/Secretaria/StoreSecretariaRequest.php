<?php

namespace App\Http\Requests\Secretaria;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSecretariaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // un administrador puede crear otro administrador, pero un doctor o secretaria no puede crear un administrador
        // $user = $this->user();
        // if ($user && $user->tieneRol('Administrador')) {
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'estado' => ['required', 'string', 'max:20', Rule::in(['activo', 'inactivo'])],
            // 'rol_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            // persona
            'carnet_identidad' => ['required', 'string', 'max:10'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['required', 'date'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'genero' => ['required', 'string', 'max:15'],
            'telefono' => ['required', 'string', 'max:20'],
            // secretaria
            'fecha_contratacion' => ['nullable', 'date'],
        ];
    }
}
