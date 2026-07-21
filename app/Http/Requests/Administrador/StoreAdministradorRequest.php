<?php

namespace App\Http\Requests\Administrador;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdministradorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //un administrador puede crear otro administrador, pero un doctor o secretaria no puede crear un administrador
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
            // 'rol_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            // persona
            'carnet_identidad' => ['required', 'string', 'max:10'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['required', 'date'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'genero' => ['required', 'string', 'max:15'],
            'telefono' => ['required', 'string', 'max:20'],
            // propietario == administrador
            'fecha_inicio' => ['nullable', 'date'],
            'porcentaje_participacion' => ['nullable', 'string', 'max:5'], 
            'estado' => ['required', 'string', 'max:20', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            // Email
            'email.required' => 'Debe ingresar el correo electrónico.',
            'email.string' => 'El correo electrónico debe ser texto.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo electrónico no debe superar los 255 caracteres.',

            // Foto / URL
            'url.string' => 'La URL de la foto debe ser texto.',
            'url.max' => 'La URL de la foto no debe superar los 255 caracteres.',

            // Contraseña
            'password.required' => 'Debe ingresar una contraseña.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',

            // Carnet
            'carnet_identidad.required' => 'Debe ingresar el carnet de identidad.',
            'carnet_identidad.string' => 'El carnet de identidad debe ser texto.',
            'carnet_identidad.max' => 'El carnet de identidad no debe superar los 10 caracteres.',

            // Nombre
            'nombre.required' => 'Debe ingresar el nombre.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no debe superar los 255 caracteres.',

            // Apellido
            'apellido.required' => 'Debe ingresar el apellido.',
            'apellido.string' => 'El apellido debe ser texto.',
            'apellido.max' => 'El apellido no debe superar los 255 caracteres.',

            // Fecha de nacimiento
            'fecha_nacimiento.required' => 'Debe ingresar la fecha de nacimiento.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',

            // Dirección
            'direccion.string' => 'La dirección debe ser texto.',
            'direccion.max' => 'La dirección no debe superar los 255 caracteres.',

            // Género
            'genero.required' => 'Debe seleccionar el género.',
            'genero.string' => 'El género debe ser texto.',
            'genero.max' => 'El género no debe superar los 15 caracteres.',

            // Teléfono
            'telefono.required' => 'Debe ingresar el teléfono.',
            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no debe superar los 20 caracteres.',

            // Fecha de inicio
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',

            // Porcentaje de participación
            'porcentaje_participacion.string' => 'El porcentaje de participación debe ser texto.',
            'porcentaje_participacion.max' => 'El porcentaje de participación no debe superar los 5 caracteres.',

            // Estado
            'estado.required' => 'Debe seleccionar el estado.',
            'estado.string' => 'El estado debe ser texto.',
            'estado.max' => 'El estado no debe superar los 20 caracteres.',
            'estado.in' => 'El estado seleccionado no es válido. Debe ser activo o inactivo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'correo electrónico',
            'url' => 'foto de perfil',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
            'carnet_identidad' => 'carnet de identidad',
            'nombre' => 'nombre',
            'apellido' => 'apellido',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'direccion' => 'dirección',
            'genero' => 'género',
            'telefono' => 'teléfono',
            'fecha_inicio' => 'fecha de inicio',
            'porcentaje_participacion' => 'porcentaje de participación',
            'estado' => 'estado',
        ];
    }
}
