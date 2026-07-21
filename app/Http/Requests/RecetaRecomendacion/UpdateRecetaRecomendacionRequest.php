<?php

namespace App\Http\Requests\RecetaRecomendacion;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRecetaRecomendacionRequest extends FormRequest
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
            'fecha_emision' => ['sometimes', 'required', 'date'],
            'observacion_general' => ['sometimes', 'nullable', 'string'],
            'tratamiento_id' => ['sometimes', 'required', 'exists:tratamientos,id'],
            'detalles' => ['sometimes', 'required', 'array', 'min:1'],
            'detalles.*.id' => ['sometimes', 'integer', 'exists:detalle_recomendacion,id'],
            'detalles.*.descripcion' => ['required_with:detalles', 'string'],
            'detalles.*.dosis' => ['required_with:detalles', 'string'],
            'detalles.*.duracion' => ['required_with:detalles', 'string'],
            'detalles.*.frecuencia' => ['required_with:detalles', 'string'],
        ];
    }
}
