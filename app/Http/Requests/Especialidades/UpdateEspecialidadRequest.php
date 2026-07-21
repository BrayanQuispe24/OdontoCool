<?php

namespace App\Http\Requests\Especialidades;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEspecialidadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'nombre' => ['sometimes','required', 'string', 'max:255'],
            'descripcion' => ['sometimes','nullable', 'string'],
            'estado' => ['sometimes','required', 'string', Rule::in(['activo', 'inactivo', 'suspendido'])],
        ];
    }
}
