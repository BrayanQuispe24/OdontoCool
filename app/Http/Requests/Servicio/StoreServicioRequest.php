<?php

namespace App\Http\Requests\Servicio;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServicioRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:100', Rule::unique('servicios')],
            'descripcion' => ['nullable', 'string'],
            'tipo' => ['required', 'string'],
            'estado' => ['sometimes','required', 'string', Rule::in(['activo', 'inactivo', 'ACTIVO', 'INACTIVO'])],
            'monto' => ['required', 'numeric', 'min:0'],
            'moneda' => ['required', 'string', 'max:3'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ];
    }
}
