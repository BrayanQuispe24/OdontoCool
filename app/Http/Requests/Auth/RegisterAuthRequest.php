<?php

namespace App\Http\Requests\Auth;

use App\Models\Rol;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // solo el administrador podra registrar cualquier tipo de usuario, el doctor o secretaria podra registrar de tipo paciente
        // $newUserRole = $this->input('rol_id');
        // $rolNuevoUsuario = Rol::find($newUserRole);

        // if ($rolNuevoUsuario && $rolNuevoUsuario->nombre === 'Paciente') {
        //     return true;
        // }

        return true; // hacer la verificacion que solo el cliente puede registrarse
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
            // paciente
            'contacto_emergencia' => ['required', 'string', 'max:20'],
            'telefono_emergencia' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Debe ingresar el correo electrónico.',
            'email.string' => 'El correo electrónico debe ser texto.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo electrónico no debe superar los 255 caracteres.',

            'url.string' => 'La URL de la foto debe ser texto.',
            'url.max' => 'La URL de la foto no debe superar los 255 caracteres.',

            'password.required' => 'Debe ingresar una contraseña.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',

            'carnet_identidad.required' => 'Debe ingresar el carnet de identidad.',
            'carnet_identidad.string' => 'El carnet de identidad debe ser texto.',
            'carnet_identidad.max' => 'El carnet de identidad no debe superar los 10 caracteres.',

            'nombre.required' => 'Debe ingresar el nombre.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no debe superar los 255 caracteres.',

            'apellido.required' => 'Debe ingresar el apellido.',
            'apellido.string' => 'El apellido debe ser texto.',
            'apellido.max' => 'El apellido no debe superar los 255 caracteres.',

            'fecha_nacimiento.required' => 'Debe ingresar la fecha de nacimiento.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no es válida.',

            'direccion.string' => 'La dirección debe ser texto.',
            'direccion.max' => 'La dirección no debe superar los 255 caracteres.',

            'genero.required' => 'Debe seleccionar el género.',
            'genero.string' => 'El género debe ser texto.',
            'genero.max' => 'El género no debe superar los 15 caracteres.',

            'telefono.required' => 'Debe ingresar el teléfono.',
            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no debe superar los 20 caracteres.',

            'contacto_emergencia.required' => 'Debe ingresar el contacto de emergencia.',
            'contacto_emergencia.string' => 'El contacto de emergencia debe ser texto.',
            'contacto_emergencia.max' => 'El contacto de emergencia no debe superar los 20 caracteres.',

            'telefono_emergencia.required' => 'Debe ingresar el teléfono de emergencia.',
            'telefono_emergencia.string' => 'El teléfono de emergencia debe ser texto.',
            'telefono_emergencia.max' => 'El teléfono de emergencia no debe superar los 20 caracteres.',
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
            'contacto_emergencia' => 'contacto de emergencia',
            'telefono_emergencia' => 'teléfono de emergencia',
        ];
    }
}
