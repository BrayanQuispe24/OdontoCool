<?php

namespace App\Http\Requests\Rol;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // agregar validacion de roles
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('roles')->ignore($this->route('rol_id'))],
            'description' => ['sometimes', 'nullable', 'string'],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
