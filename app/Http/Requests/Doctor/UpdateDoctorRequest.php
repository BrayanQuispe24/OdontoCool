<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //un administrador puede actualizar otro administrador, pero un doctor o secretaria no puede actualizar un administrador
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
            'email' => ['sometimes','required', 'string', 'email', 'max:255'],
            'url' => ['sometimes','nullable', 'string', 'max:255'],
            //'password' => ['sometimes','required', 'string', 'min:8', 'confirmed'],
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
            // doctor
            'matricula_profesional' => ['sometimes','required', 'max:20'],
            'experiencia' => ['sometimes','nullable', 'integer', 'min:0'],
            'telefono_profesional' => ['sometimes','nullable', 'string', 'max:15'],
            'fecha_contratacion' => ['sometimes','nullable', 'date'],
        ];
    }
}
