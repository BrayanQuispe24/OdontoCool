<?php

namespace App\Http\Requests\Servicio;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServicioRequest extends FormRequest
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
            'nombre' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('servicios')->ignore($this->route('servicio_id'))],
            'descripcion' => ['sometimes', 'nullable', 'string'],
            'tipo' => ['sometimes', 'required', 'string'],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['activo', 'inactivo', 'ACTIVO', 'INACTIVO'])],
            'monto' => ['sometimes', 'required', 'numeric', 'min:0'],
            'moneda' => ['sometimes', 'required', 'string', 'max:3'],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin' => ['sometimes', 'required', 'date', 'after_or_equal:fecha_inicio'],
        ];
    }
}
